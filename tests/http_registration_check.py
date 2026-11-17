import http.cookiejar
import json
import re
import time
import urllib.error
import urllib.parse
import urllib.request

BASE = 'http://127.0.0.1:8182'
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))


def request(method, path, fields=None):
    data = None if fields is None else urllib.parse.urlencode(fields).encode()
    req = urllib.request.Request(BASE + path, data=data, method=method)
    try:
        response = opener.open(req, timeout=5)
    except urllib.error.HTTPError as error:
        response = error
    return response.status, response.headers, response.read().decode('utf-8', errors='replace')


for attempt in range(30):
    try:
        status, headers, html = request('GET', '/signin')
        break
    except urllib.error.URLError:
        time.sleep(.2)
else:
    raise RuntimeError('Сервер Laravel не запустился')

assert status == 200
match = re.search(r'name="_token" value="([^"]+)"', html)
assert match, 'CSRF-токен должен быть в форме'
token = match.group(1)

valid = {'name': 'Алина', 'email': 'alina@example.com', 'password': 'SafePass123!'}
status, headers, body = request('POST', '/signin', valid)
assert status == 419, f'Без CSRF-токена ожидали 419, получили {status}'

status, headers, body = request('POST', '/signin', dict(valid, _token=token))
assert status == 200, f'Ожидали 200, получили {status}: {body[:400]}'
assert headers.get('Content-Type', '').startswith('application/json')
parsed = json.loads(body)
assert parsed['user']['name'] == valid['name']
assert parsed['user']['email'] == valid['email']
assert parsed['user']['password'] == '[скрыт]'
assert valid['password'] not in body

print('HTTP-проверки Laravel: форма, CSRF (419), POST, JSON и защита пароля — успешно')
