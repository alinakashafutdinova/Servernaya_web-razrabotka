"""Проверка очереди модерации, прав и CSRF на HTTP-сервере Laravel."""
import http.cookiejar
import os
import re
import sqlite3
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

BASE = 'http://127.0.0.1:8182'
DATABASE = Path(__file__).resolve().parents[1] / 'database/database.sqlite'


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, request, fp, code, msg, headers, newurl):
        return None


def new_client():
    return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()), NoRedirect())


def request(client, path, data=None):
    payload = urllib.parse.urlencode(data).encode() if data is not None else None
    req = urllib.request.Request(BASE + path, data=payload)
    try:
        response = client.open(req, timeout=12)
    except urllib.error.HTTPError as error:
        response = error
    return response.status, response.headers, response.read().decode('utf-8', 'replace')


def csrf(client, path):
    status, _, html = request(client, path)
    assert status == 200, (path, status, html[:220])
    match = re.search(r'name="_token"\s+value="([A-Za-z0-9]+)"', html)
    assert match, 'Нет CSRF-токена'
    return match.group(1)


def latest_comment_id(body):
    with sqlite3.connect(DATABASE) as connection:
        row = connection.execute('SELECT id FROM comments WHERE body=? ORDER BY id DESC LIMIT 1', (body,)).fetchone()
        return row[0] if row else None


visitor, reader, moderator = new_client(), new_client(), new_client()
for attempt in range(35):
    try:
        if request(visitor, '/news')[0] == 200:
            break
    except urllib.error.URLError:
        time.sleep(.2)
else:
    raise RuntimeError('Не запустился тестовый сервер')

assert request(visitor, '/moderation')[0] == 302
assert request(visitor, '/comments/1/approve', {'_method':'PATCH'})[0] == 302
register_csrf = csrf(reader, '/register')
status, headers, _ = request(reader, '/register', {
    '_token': register_csrf,
    'name': 'Читатель для теста',
    'email': 'reader-moderation@example.test',
    'password': 'ReaderPassword123!',
    'password_confirmation': 'ReaderPassword123!',
})
assert status == 302 and '/login' in headers.get('Location', ''), status
login_csrf = csrf(reader, '/login')
status, _, _ = request(reader, '/login', {
    '_token': login_csrf,
    'email': 'reader-moderation@example.test',
    'password': 'ReaderPassword123!',
})
assert status == 302
assert request(reader, '/moderation')[0] == 403
assert 'href="'+BASE+'/moderation"' not in request(reader, '/news/1')[2]

body = '<script>test()</script> ожидающий комментарий'
reader_csrf = csrf(reader, '/news/1')
status, headers, _ = request(reader, '/news/1/comments', {'_token': reader_csrf, 'body': body})
assert status == 302 and '/news/1' in headers.get('Location', ''), (status, headers.get('Location'))
comment_id = latest_comment_id(body)
assert comment_id is not None
with sqlite3.connect(DATABASE) as connection:
    assert connection.execute('SELECT is_approved FROM comments WHERE id=?', (comment_id,)).fetchone()[0] == 0
assert 'ожидает модерации' in request(reader, '/news/1')[2]
assert 'ожидающий комментарий' not in request(visitor, '/news/1')[2]
assert request(reader, f'/comments/{comment_id}/approve', {'_method': 'PATCH', '_token': reader_csrf})[0] == 403
assert request(reader, f'/comments/{comment_id}/reject', {'_method': 'DELETE', '_token': reader_csrf})[0] == 403

mod_csrf = csrf(moderator, '/login')
status, _, _ = request(moderator, '/login', {
    '_token': mod_csrf,
    'email': os.environ.get('MODERATOR_EMAIL', 'moderator@example.test'),
    'password': os.environ['MODERATOR_PASSWORD'],
})
assert status == 302, 'Модератор не вошёл'
status, _, queue = request(moderator, '/moderation')
assert status == 200 and 'ожидающий комментарий' in queue and '&lt;script&gt;' in queue
assert 'Модерация' in request(moderator, '/')[2]
mod_csrf = csrf(moderator, '/moderation')
assert request(moderator, f'/comments/{comment_id}/approve', {'_method':'PATCH'})[0] == 419
assert request(moderator, f'/comments/{comment_id}/approve')[0] == 405
status, headers, _ = request(moderator, f'/comments/{comment_id}/approve', {'_token':mod_csrf,'_method':'PATCH'})
assert status == 302 and '/moderation' in headers.get('Location', '')
assert 'ожидающий комментарий' not in request(moderator, '/moderation')[2]
assert 'ожидающий комментарий' in request(visitor, '/news/1')[2]
assert '<script>test()</script>' not in request(visitor, '/news/1')[2]
assert request(moderator, f'/comments/{comment_id}/reject', {'_token':mod_csrf, '_method':'DELETE'})[0] == 404

reject_body = 'Этот текст нужно отклонить'
status, _, _ = request(reader, '/news/1/comments', {'_token':reader_csrf,'body':reject_body})
assert status == 302
rejected_id = latest_comment_id(reject_body)
assert rejected_id is not None
assert reject_body not in request(visitor, '/news/1')[2]
status, _, _ = request(moderator, f'/comments/{rejected_id}/reject', {'_token':mod_csrf,'_method':'DELETE'})
assert status == 302
assert latest_comment_id(reject_body) is None
assert reject_body not in request(visitor, '/news/1')[2]
print('HTTP-модерация: очередь, роли, CSRF, подтверждение и отклонение комментариев — успешно')
