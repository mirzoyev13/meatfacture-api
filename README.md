Masofaktura API
RESTful API для управления заказами в приложении «Мясофактура». Реализовано на Laravel с использованием JWT, Swagger, модульной архитектуры и покрытием тестами, фейкерами и сидорами.


Установка
git clone ...
cd masofaktura
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan jwt:secret
php artisan serve

Авторизация
После входа (/api/login) вы получите JWT токен
Передавайте токен в заголовке:
Authorization: Bearer <token>

Swagger-документация
http://127.0.0.1:8000/api/documentation

Архитектура
Контроллеры только маршрутизируют логику
Отдельные классы для валидации
Ответы API строго RESTful, с статусами 200, 201, 401, 422, 500
Все заказы создаются внутри транзакции
Пользователь никогда не передаёт user_id вручную

Unit тесты
Покрыты основные сценарии:

Запуск:
php artisan test
murad@MacBook-Pro-Murad masofaktura % php artisan test


PASS  Tests\Unit\ExampleTest
✓ that true is true

PASS  Tests\Feature\AuthTest
✓ register creates user                                                                                                                                            0.34s  
✓ login with correct credentials                                                                                                                                   0.02s  
✓ login fails with invalid credentials                                                                                                                             0.02s

PASS  Tests\Feature\ExampleTest
✓ the application returns a successful response                                                                                                                    0.02s

PASS  Tests\Feature\OrderTest
✓ guest cannot create order                                                                                                                                        0.01s  
✓ authenticated user can create order                                                                                                                              0.02s  
✓ order requires items array                                                                                                                                       0.01s  
✓ user only sees own orders                                                                                                                                        0.01s

Tests:    9 passed (15 assertions)
Duration: 0.51s


Стек:
Laravel 10+
JWT (tymon/jwt-auth)
Swagger (l5-swagger)
Eloquent ORM + миграции + сидеры + фейкры
REST API (auth, CRUD, отношения)
Feature/Unit Tests
