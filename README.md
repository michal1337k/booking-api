# Booking API Symfony Project

A Symfony project running on Docker, used to manage appointments (hairdresser, doctor, mechanic, online consultations) via a REST API with JWT authentication.

---

## Technologies:
- Symfony 6+
- PHP 8+
- Doctrine ORM + MySQL 8
- API Platform (optional)
- LexikJWTAuthenticationBundle (JWT Auth)
- Docker + Docker Compose
- Postman for API testing

---

## Setup (Docker):

1. Clone the repository and navigate to the project folder:
   ```bash
   git clone https://github.com/michal1337k/booking-api.git
   cd booking-api
   ```
   
2. Copy the *.env* file and configure environment variables:
   ```bash
   cp .env.example .env
    ```
   - *DATABASE_URL* → adres bazy w Dockerze (mysql://root:root@db:3306/booking)
   - *JWT_PASSPHRASE* → hasło do klucza JWT
   - *APP_SECRET* 

3. Start Docker:
    ```bash
   docker-compose up -d
    ```
    
4. Run migrations and insert test users:
   ```bash
   docker-compose exec php php bin/console doctrine:migrations:migrate
   docker-compose exec php mysql -uroot -proot booking -e "
   INSERT INTO user (email, password, roles) VALUES 
   ('admin@admin.pl', '\$2y\$13\$jrkBzmt7PlnCvY9D1iQQHuEnHv99iWqi3POhMQWWZKcbPngzEuZI', '[\"ROLE_ADMIN\"]'),
   ('test@test.pl', '\$2y\$13\$jrkBzmt7PlnCvY9D1iQQHuEnHv99iWqi3POhMQWWZKcbPngzEuZI', '[\"ROLE_USER\"]');"
   ```
   Password for user: *test*
   Password for admin: *admin*

6. Application is available at:
   ```bash
   http://localhost:8000
   ```
---

## API Endpoints:

| Path | Description | Role |
| -------- | ------- | ------- |
| POST /api/login | Log in user → returns JWT token | anonymous |
| GET /api/slots | List all slots + info if booked (isBooked) | authenticated |
| POST  /api/slots | Add a new slot | admin |
| GET /api/bookings | List bookings for logged-in user | authenticated |
| POST /api/bookings | Create a new booking | authenticated |
| DELETE /api/bookings/{id} | Delete a booking (owner or admin) | owner/admin |

--- 

## Examples

1. Login (get JWT Token)
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "admin@admin.pl",
  "password": "admin"
}
```
Response:
```bash
{
  "token": "eyJ0eXAiOiJKV1QiLCJh..."
}
```
2. Get slots
```bash
GET http://localhost:8000/api/slots
Authorization: Bearer <JWT_TOKEN>
```
Response:
```bash
[
  {
    "id": 1,
    "startAt": "2026-04-01 10:00",
    "endAt": "2026-04-01 10:30",
    "isBooked": false
  }
]
```
3. Create a booking
```bash
POST http://localhost:8000/api/bookings
Authorization: Bearer <JWT_TOKEN>
Content-Type: application/json

{
  "slot_id": 1
}
```
Response:
```bash
{
  "id": 1,
  "slot": {
    "startAt": "2026-04-01 10:00",
    "endAt": "2026-04-01 10:30"
  },
  "status": "booked"
}
```
4. Delete a booking
```bash
DELETE http://localhost:8000/api/bookings/1
Authorization: Bearer <JWT_TOKEN>
```
- **204** → success
- **403** → forbidden (not owner/admin)

---

## Testing
Use two test accounts for full coverage:
- admin@admin.pl → admin (can add slots, delete any booking)
- test@test.pl → user (can book slots, delete only own bookings)
  
Recommended: Postman workflow: login → get JWT token → use Authorization header for subsequent requests

---

## Notes
- Slot conflict validation works → a slot cannot be double-booked
- *roles* in DB → JSON, in PHP *array*
- JWT tokens expire in 3600s (1 hour)
- All */api* endpoints require JWT token, except */api/login*
