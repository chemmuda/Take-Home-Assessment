# Laravel Backend Test Suite

This directory contains comprehensive automated tests for the Laravel backend API.

## Test Structure

```
tests/
├── Unit/                           # Unit tests for business logic
│   ├── UserTest.php               # User model tests
│   ├── ProductTest.php            # Product model tests
│   ├── OrderTest.php              # Order model tests
│   └── OrderItemTest.php          # OrderItem model tests
├── Feature/
│   ├── Api/                       # Integration tests for API endpoints
│   │   ├── AuthenticationTest.php # Auth endpoint tests
│   │   ├── UserTest.php          # User API tests
│   │   ├── ProductTest.php       # Product API tests
│   │   └── OrderTest.php         # Order API tests
│   └── Workflows/                 # Feature tests for complete workflows
│       ├── AuthenticationWorkflowTest.php
│       ├── OrderWorkflowTest.php
│       └── UserManagementWorkflowTest.php
├── TestCase.php                   # Base test case with helper methods
└── README.md                      # This file
```

## Running Tests

### Run All Tests
```bash
php artisan test
```

Or using PHPUnit directly:
```bash
./vendor/bin/phpunit
```

### Run Specific Test Suites

**Unit Tests Only:**
```bash
php artisan test --testsuite=Unit
```

**Feature Tests Only:**
```bash
php artisan test --testsuite=Feature
```

**Specific Test File:**
```bash
php artisan test tests/Unit/UserTest.php
```

**Specific Test Method:**
```bash
php artisan test --filter test_method_name
```

## Code Coverage

### Generate Coverage Report
```bash
php artisan test --coverage
```

### Generate HTML Coverage Report
```bash
./vendor/bin/phpunit --coverage-html tests/coverage
```

The HTML report will be generated in `tests/coverage/index.html`.

### Check Coverage Minimum
```bash
./vendor/bin/phpunit --coverage-text --coverage-html tests/coverage
```

**Target:** Minimum 80% code coverage

## Test Categories

### 1. Unit Tests (tests/Unit/)
Tests individual model methods and business logic in isolation:

- **UserTest.php**
  - `isAdmin()` method functionality
  - User-Order relationships
  - Hidden attributes (password, remember_token)
  - Fillable attributes
  - Default role assignment

- **ProductTest.php**
  - `isInStock()` method functionality
  - Product-OrderItem relationships
  - Fillable attributes
  - Stock status validation

- **OrderTest.php**
  - Order-User relationships
  - Order-OrderItem relationships
  - `calculateTotal()` method functionality
  - Order status management

- **OrderItemTest.php**
  - OrderItem-Order relationships
  - OrderItem-Product relationships
  - Price snapshot functionality

### 2. Integration Tests (tests/Feature/Api/)
Tests API endpoints with authentication and data validation:

- **AuthenticationTest.php** (15 tests)
  - User registration (valid/invalid data)
  - User login (valid/invalid credentials)
  - Logout functionality
  - Token authentication
  - Profile access

- **UserTest.php** (13 tests)
  - List users (admin/regular user)
  - View user profile
  - Update user profile
  - Delete user
  - Authorization checks

- **ProductTest.php** (11 tests)
  - List products
  - View single product
  - Create product
  - Update product
  - Delete product
  - Validation tests

- **OrderTest.php** (14 tests)
  - List orders
  - View single order
  - Create order with multiple items
  - Update order status
  - Order total calculation
  - Order statistics by status
  - Order-user relationships

### 3. Feature/Workflow Tests (tests/Feature/Workflows/)
Tests complete user workflows from start to finish:

- **OrderWorkflowTest.php**
  - Complete order creation workflow
  - Order cancellation workflow
  - Multiple orders per user
  - Product validation
  - Total calculation verification

- **UserManagementWorkflowTest.php**
  - Complete user lifecycle (create, view, update, delete)
  - Regular user restrictions
  - Admin privileges
  - Data integrity checks
  - Privilege escalation prevention

- **AuthenticationWorkflowTest.php**
  - Complete registration and authentication flow
  - Login with existing user
  - Failed authentication scenarios
  - Multiple sessions management
  - Duplicate email prevention

## Database Factories

Factories are located in `database/factories/` and provide test data generation:

- **UserFactory.php** - Creates test users with configurable roles
- **ProductFactory.php** - Creates test products with stock management
- **OrderFactory.php** - Creates test orders with various statuses
- **OrderItemFactory.php** - Creates test order items with products

### Using Factories in Tests

```php
// Create a single user
$user = User::factory()->create();

// Create an admin user
$admin = User::factory()->admin()->create();

// Create 5 products
$products = Product::factory()->count(5)->create();

// Create an order with items
$order = Order::factory()
    ->has(OrderItem::factory()->count(3))
    ->create();
```

## Helper Methods (TestCase.php)

The base `TestCase` class provides helper methods:

- `createAdminUser()` - Creates and authenticates an admin user
- `createUser()` - Creates and authenticates a regular user
- `getAuthHeaders($user)` - Returns authentication headers with Bearer token

### Usage Example

```php
public function test_example()
{
    $user = $this->createUser();
    
    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/products');
    
    $response->assertStatus(200);
}
```

## Test Coverage Goals

The test suite aims for minimum 80% code coverage across:

- ✅ Models (User, Product, Order, OrderItem)
- ✅ Controllers (Auth, User, Product, Order)
- ✅ API Endpoints (all routes in api.php)
- ✅ Business Logic (calculations, validations)
- ✅ Authentication & Authorization

## Best Practices

1. **Isolation**: Each test is isolated with `RefreshDatabase` trait
2. **Descriptive Names**: Test methods clearly describe what they test
3. **Arrange-Act-Assert**: Tests follow AAA pattern
4. **Data Factories**: Use factories for consistent test data
5. **Assertions**: Multiple relevant assertions per test
6. **Edge Cases**: Tests cover happy path and edge cases

## Continuous Integration

These tests are designed to run in CI/CD pipelines:

```yaml
# Example GitHub Actions workflow
- name: Run Tests
  run: php artisan test --coverage --min=80
```

## Troubleshooting

### Database Issues
If you encounter database errors:
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Cache Issues
Clear application cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Dependency Issues
Reinstall dependencies:
```bash
composer install
```

## Contributing

When adding new features:
1. Write tests first (TDD approach)
2. Ensure tests pass: `php artisan test`
3. Maintain 80%+ coverage
4. Follow existing test patterns
5. Update this README if adding new test categories

## Test Statistics

- **Total Tests**: 70+
- **Unit Tests**: 24
- **Integration Tests**: 53+
- **Workflow Tests**: 13+
- **Target Coverage**: 80%+
- **Execution Time**: ~10-30 seconds

## Documentation

For more information about testing in Laravel:
- [Laravel Testing Documentation](https://laravel.com/docs/10.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Database Testing](https://laravel.com/docs/10.x/database-testing)
