"""Проверка CRUD-форм и CSRF через настоящий HTTP-сервер."""
import http.cookiejar
import re
import sqlite3
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

BASE = 'http://127.0.0.1:8182'


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, request, fp, code, msg, headers, newurl):
        return None


client = urllib.request.build_opener(
    urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), NoRedirect()
)


def request(path, data=None):
    body = urllib.parse.urlencode(data).encode() if data is not None else None
    req = urllib.request.Request(BASE + path, data=body)
    try:
        response = client.open(req, timeout=12)
    except urllib.error.HTTPError as error:
        response = error
    return response.status, response.headers, response.read().decode('utf-8', 'replace')


def csrf_token(path):
    status, headers, body = request(path)
    assert status == 200, (path, status, body[:200])
    token = re.search(r'name="_token"\s+value="([A-Za-z0-9]+)"', body)
    assert token is not None, 'Отсутствует CSRF-токен'
    return token.group(1)


def article_id(title):
    connection = sqlite3.connect(Path(__file__).resolve().parents[1] / 'database/database.sqlite')
    try:
        row = connection.execute('SELECT id FROM articles WHERE title = ? ORDER BY id DESC LIMIT 1', (title,)).fetchone()
        return row[0] if row else None
    finally:
        connection.close()


for attempt in range(30):
    try:
        token = csrf_token('/news/create')
        break
    except urllib.error.URLError:
        time.sleep(.2)
else:
    raise RuntimeError('HTTP-сервер не запустился')

form = {
    'title': 'Автоматическая проверка CRUD',
    'summary': 'Проверяю обработку данных через сервер и форму.',
    'content': 'Достаточно длинный текст статьи для HTTP-тестирования создания.',
    'published_at': '2026-11-18',
    'is_published': '1',
}
status, headers, _ = request('/news', form)
assert status == 419, ('Без CSRF', status)
status, headers, _ = request('/news', {**form, 'title': '', '_token': token})
assert status == 302, ('Неверные данные', status)
assert article_id(form['title']) is None, 'Создание с ошибочными данными'
status, headers, _ = request('/news', {**form, '_token': token})
assert status == 302, ('Создание', status)
assert '/management' in headers.get('Location', '')
id = article_id(form['title'])
assert id is not None, 'Статья не сохранена'
assert form['title'] in request('/management')[2]
assert form['title'] in request('/news')[2]

token = csrf_token(f'/news/{id}/edit')
updated_title = 'Статья после HTTP-редактирования'
status, _, _ = request(f'/news/{id}', {**form, 'title': updated_title, '_token': token, '_method': 'PUT', 'is_published': '0'})
assert status == 302, ('Редактирование', status)
assert article_id(updated_title) == id, 'Редактирование не сохранено'
assert request(f'/news/{id}')[0] == 404, 'Черновик доступен публично'

status, _, _ = request(f'/news/{id}', {'_method': 'DELETE'})
assert status == 419, ('Удаление без CSRF', status)
assert article_id(updated_title) == id, 'Удаление без токена сработало'
status, _, _ = request(f'/news/{id}', {'_method': 'DELETE', '_token': token})
assert status == 302, ('Удаление', status)
assert article_id(updated_title) is None, 'Статья не удалена'
print('HTTP CRUD + CSRF: проверка создания, обновления, удаления и защиты пройдена')
