## 🔧 Requirements

- PHP >= 8.1
- Composer
- MySQL
- Laravel ^12.0

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/vehicle-expenses-api.git
cd vehicle-expenses-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vehicle_expenses
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

## 🗄️ Database Setup

### 1. Create Database

```bash
mysql -u root -p
CREATE DATABASE vehicle_expenses;
EXIT;
```

### 2. Import the Provided Database Sample

Download the database sample from:
```
https://drive.google.com/file/d/14h7-ouoeovbiUhY-p93FjbHWEbLX-FZW/view?usp=sharing
```

Import it:

```bash
mysql -u your_username -p vehicle_expenses < database_sample.sql
```

### Development Server

```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

---

## API Documentation

### Endpoint

```
GET /api/vehicle-expenses
```

### Response Structure

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "vehicle_name": "Rey Murray",
      "plate_number": "ABC-123",
      "type": "fuel",
      "cost": 150.50,
      "created_at": "2025-01-15"
    },
    {
      "id": 2,
      "vehicle_name": "Honda Civic",
      "plate_number": "XYZ-789",
      "type": "insurance",
      "cost": 2000.00,
      "created_at": "2025-01-10"
    }
  ],
  "meta": {
    "total": 2
  }
}
```

---

## Query Parameters

### Search

Search by vehicle name (partial match):

```bash
GET /api/vehicle-expenses?search=Rey Murray
```

**Example:**
```bash
curl "http://localhost:8000/api/vehicle-expenses?search=Camry"
```

---

### Filtering

#### Filter by Expense Type

Filter by one or more types: `fuel`, `insurance`, `service`

**Single type:**
```bash
GET /api/vehicle-expenses?types[]=fuel
```

**Multiple types:**
```bash
GET /api/vehicle-expenses?types[]=fuel&types[]=insurance
```

**Example:**
```bash
curl "http://localhost:8000/api/vehicle-expenses?types[]=fuel&types[]=service"
```

#### Filter by Cost Range

**Minimum cost:**
```bash
GET /api/vehicle-expenses?min_cost=100
```

**Maximum cost:**
```bash
GET /api/vehicle-expenses?max_cost=1000
```

**Cost range:**
```bash
GET /api/vehicle-expenses?min_cost=100&max_cost=1000
```

**Example:**
```bash
curl "http://localhost:8000/api/vehicle-expenses?min_cost=500&max_cost=2000"
```

#### Filter by Date Range

**Minimum date:**
```bash
GET /api/vehicle-expenses?min_date=2025-01-01
```

**Maximum date:**
```bash
GET /api/vehicle-expenses?max_date=2025-12-31
```

**Date range:**
```bash
GET /api/vehicle-expenses?min_date=2025-01-01&max_date=2025-12-31
```

**Example:**
```bash
curl "http://localhost:8000/api/vehicle-expenses?min_date=2025-01-01&max_date=2025-06-30"
```

---

### Sorting

#### Sort by Cost

**Ascending:**
```bash
GET /api/vehicle-expenses?sort_by=cost&sort_direction=asc
```

**Descending:**
```bash
GET /api/vehicle-expenses?sort_by=cost&sort_direction=desc
```

#### Sort by Date

**Ascending (oldest first):**
```bash
GET /api/vehicle-expenses?sort_by=created_at&sort_direction=asc
```

**Descending (newest first) - Default:**
```bash
GET /api/vehicle-expenses?sort_by=created_at&sort_direction=desc
```

**Example:**
```bash
curl "http://localhost:8000/api/vehicle-expenses?sort_by=cost&sort_direction=asc"
```

---

### Combined Examples

Get all fuel and service expenses for vehicles containing "Rey Murray", costing between $100-$500, from January 2025, sorted by cost ascending:

```bash
curl "http://localhost:8000/api/vehicle-expenses?search=Rey Murray&types[]=fuel&types[]=service&min_cost=100&max_cost=500&min_date=2025-01-01&max_date=2025-01-31&sort_by=cost&sort_direction=asc"
```

**Using Postman:**

```
GET http://localhost:8000/api/vehicle-expenses

Query Params:
- search: Rey Murray
- types[]: fuel
- types[]: service
- min_cost: 100
- max_cost: 500
- min_date: 2025-01-01
- max_date: 2025-01-31
- sort_by: cost
- sort_direction: asc
```

---

## 🔒 Rate Limiting

The endpoint is limited to **5 requests per minute** per IP address.

**Rate Limit Exceeded Response:**

```json
{
  "success": false,
  "message": "Too many requests. Please try again in 45 seconds."
}
```

**Status Code:** `429 Too Many Requests`

---

## Testing

### Run All Tests

```bash
cp .env.example .env.testing

php artisan test
```

## Architecture

### Directory Structure

```
app/
├── Contracts/              # Interfaces
│   ├── ExpenseRepositoryInterface.php
│   └── ExpenseTransformerInterface.php
├── DTOs/                   # Data Transfer Objects
│   ├── ExpenseFilterDTO.php
│   └── VehicleExpenseDTO.php
├── Http/
│   ├── Controllers/Api/
│   │   └── VehicleExpenseController.php
│   ├── Middleware/
│   │   └── CustomThrottle.php
│   ├── Requests/
│   │   └── VehicleExpenseRequest.php
│   └── Resources/
│       ├── VehicleExpenseResource.php
│       └── VehicleExpenseCollection.php
├── Models/                 # Eloquent Models
│   ├── Vehicle.php
│   ├── FuelEntry.php
│   ├── InsurancePayment.php
│   └── Service.php
├── Repositories/           # Data Access Layer
│   └── VehicleExpenseRepository.php
├── Services/               # Business Logic
│   └── VehicleExpenseService.php
└── Transformers/           # Data Transformation
    └── ExpenseTransformer.php
```

---

### Design Patterns Used

- **Repository Pattern**: Abstracts data access logic
- **Data Transfer Object (DTO)**: Type-safe data containers
- **Transformer Pattern**: Converts database models to API responses
- **Service Layer Pattern**: Encapsulates business logic
- **Dependency Injection**: Loose coupling between components

---

### Future Enhancements

- Enhance testing coverage to include all possible test cases
- Export to CSV/Excel
- Caching layer for frequently accessed data
- API versioning
- Real-time notifications

---
