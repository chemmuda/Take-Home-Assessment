# Test Plan - Laravel Backend API
## Comprehensive Test Suite (80+ Test Cases)

## Authentication Module (20 Test Cases)

### TC-AUTH-001: User Registration - Valid Data
**Priority**: High  
**Module**: Authentication  
**Preconditions**: API server running, database clean  
**Test Steps**:
1. Send POST request to /api/register
2. Provide valid user data: {name: "John Doe", email: "john@test.com", password: "Password123!", password_confirmation: "Password123!"}
3. Verify response  
**Expected Result**: 201 Created with user data and token
**Actual Result**: 
**Status**: Pass/Fail

### TC-AUTH-002: User Registration - Duplicate Email
**Priority**: High  
**Test Steps**:
1. Register user with email "user@test.com"
2. Attempt registration with same email
3. Verify response
**Expected**: 422 Validation error with email duplication message

### TC-AUTH-003: User Registration - Invalid Email Format
**Priority**: Medium  
**Test Steps**:
1. Send POST to /api/register with email "invalid-email"
2. Verify response
**Expected**: 422 Validation error for invalid email

### TC-AUTH-004: User Registration - Weak Password
**Priority**: Medium  
**Test Steps**:
1. Send POST to /api/register with password "123"
2. Verify response
**Expected**: 422 Validation error for weak password

### TC-AUTH-005: User Registration - Missing Required Fields
**Priority**: High  
**Test Steps**:
1. Send POST to /api/register with only email field
2. Verify response
**Expected**: 422 Validation error for missing name and password

### TC-AUTH-006: User Registration - Password Confirmation Mismatch
**Priority**: Medium  
**Test Steps**:
1. Send POST to /api/register with password "Password123!" and password_confirmation "DifferentPassword123!"
2. Verify response
**Expected**: 422 Validation error for password confirmation

### TC-AUTH-007: User Login - Valid Credentials
**Priority**: High  
**Test Steps**:
1. Create user with email "login@test.com", password "Password123!"
2. Send POST to /api/login with valid credentials
3. Verify response
**Expected**: 200 OK with user data and token

### TC-AUTH-008: User Login - Invalid Email
**Priority**: Medium  
**Test Steps**:
1. Send POST to /api/login with non-existent email
2. Verify response
**Expected**: 401 Unauthorized with error message

### TC-AUTH-009: User Login - Invalid Password
**Priority**: Medium  
**Test Steps**:
1. Create user with email "user@test.com", password "Password123!"
2. Send POST to /api/login with wrong password
3. Verify response
**Expected**: 401 Unauthorized with error message

### TC-AUTH-010: User Login - Missing Credentials
**Priority**: Medium  
**Test Steps**:
1. Send POST to /api/login with empty body
2. Verify response
**Expected**: 422 Validation error for missing fields

### TC-AUTH-011: User Logout - Authenticated User
**Priority**: High  
**Preconditions**: User logged in, token available
**Test Steps**:
1. Send POST to /api/logout with Authorization header
2. Verify response
3. Attempt to access protected route with same token
**Expected**: 200 OK on logout, 401 on subsequent requests

### TC-AUTH-012: User Logout - Without Authentication
**Priority**: Medium  
**Test Steps**:
1. Send POST to /api/logout without Authorization header
2. Verify response
**Expected**: 401 Unauthorized

### TC-AUTH-013: Get User Profile - Authenticated
**Priority**: High  
**Test Steps**:
1. Login and get token
2. Send GET to /api/user with Authorization header
3. Verify response
**Expected**: 200 OK with current user data

### TC-AUTH-014: Get User Profile - Unauthenticated
**Priority**: Medium  
**Test Steps**:
1. Send GET to /api/user without token
2. Verify response
**Expected**: 401 Unauthorized

### TC-AUTH-015: Token Expiry Validation
**Priority**: Low  
**Test Steps**:
1. Login and get token
2. Wait for token expiry (if configured)
3. Attempt to access protected route
**Expected**: 401 Unauthorized after expiry

### TC-AUTH-016: Concurrent Logins
**Priority**: Low  
**Test Steps**:
1. Login from multiple devices simultaneously
2. Verify all sessions work independently
**Expected**: Multiple valid sessions allowed

### TC-AUTH-017: Login Case Sensitivity
**Priority**: Low  
**Test Steps**:
1. Create user with email "Test@Example.COM"
2. Login with "test@example.com"
3. Verify response
**Expected**: Case-insensitive email matching

### TC-AUTH-018: Registration Email Trim
**Priority**: Low  
**Test Steps**:
1. Register with email "  test@test.com  " (with spaces)
2. Verify stored email
**Expected**: Email stored without leading/trailing spaces

### TC-AUTH-019: Password Hashing Verification
**Priority**: Medium  
**Test Steps**:
1. Register new user
2. Check database for password storage
**Expected**: Password stored as hash, not plain text

### TC-AUTH-020: Session Persistence
**Priority**: Medium  
**Test Steps**:
1. Login and get token
2. Make multiple requests with same token over time
3. Verify session persists
**Expected**: Token remains valid until logout/expiry

---

## User Management Module (25 Test Cases)

### TC-USER-001: List All Users - Admin Role
**Priority**: High  
**Preconditions**: Admin user exists and logged in
**Test Steps**:
1. Send GET to /api/users with admin token
2. Verify response
**Expected**: 200 OK with paginated user list

### TC-USER-002: List All Users - Regular User Role
**Priority**: High  
**Test Steps**:
1. Send GET to /api/users with regular user token
2. Verify response
**Expected**: 403 Forbidden

### TC-USER-003: List All Users - Unauthenticated
**Priority**: Medium  
**Test Steps**:
1. Send GET to /api/users without token
2. Verify response
**Expected**: 401 Unauthorized

### TC-USER-004: Get User by ID - Admin
**Priority**: High  
**Test Steps**:
1. Create test user
2. Send GET to /api/users/{id} with admin token
3. Verify response
**Expected**: 200 OK with user details

### TC-USER-005: Get User by ID - Same User
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Send GET to /api/users/{own_id}
3. Verify response
**Expected**: 200 OK with own user details

### TC-USER-006: Get User by ID - Different Regular User
**Priority**: High  
**Test Steps**:
1. Login as user A
2. Attempt to get user B's details
3. Verify response
**Expected**: 403 Forbidden

### TC-USER-007: Get User by ID - Non-existent User
**Priority**: Medium  
**Test Steps**:
1. Send GET to /api/users/99999 with admin token
2. Verify response
**Expected**: 404 Not Found

### TC-USER-008: Get User by ID - Invalid ID Format
**Priority**: Low  
**Test Steps**:
1. Send GET to /api/users/abc with admin token
2. Verify response
**Expected**: 404 Not Found or 422 Validation error

### TC-USER-009: Update User - Admin Updates Other User
**Priority**: High  
**Test Steps**:
1. Login as admin
2. Send PUT to /api/users/{user_id} with update data
3. Verify response and database
**Expected**: 200 OK, user updated in database

### TC-USER-010: Update User - User Updates Self
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Send PUT to /api/users/{own_id} with update data
3. Verify response
**Expected**: 200 OK, own profile updated

### TC-USER-011: Update User - User Updates Other User
**Priority**: High  
**Test Steps**:
1. Login as user A
2. Attempt to update user B's profile
3. Verify response
**Expected**: 403 Forbidden

### TC-USER-012: Update User - Invalid Data
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Send PUT with invalid email format
3. Verify response
**Expected**: 422 Validation error

### TC-USER-013: Update User - Duplicate Email
**Priority**: Medium  
**Test Steps**:
1. Create two users: userA@test.com, userB@test.com
2. Update userA with userB's email
3. Verify response
**Expected**: 422 Validation error for duplicate email

### TC-USER-014: Update User - Partial Update
**Priority**: Medium  
**Test Steps**:
1. Login as user
2. Send PUT with only name field
3. Verify response and that other fields unchanged
**Expected**: 200 OK, only name updated

### TC-USER-015: Delete User - Admin Deletes Other User
**Priority**: High  
**Test Steps**:
1. Login as admin
2. Send DELETE to /api/users/{user_id}
3. Verify response and database
**Expected**: 200 OK, user deleted from database

### TC-USER-016: Delete User - User Deletes Self
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Send DELETE to /api/users/{own_id}
3. Verify response
**Expected**: 200 OK or 403 based on business rules

### TC-USER-017: Delete User - User Deletes Other User
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Attempt to delete another user
3. Verify response
**Expected**: 403 Forbidden

### TC-USER-018: Delete User - Non-existent User
**Priority**: Low  
**Test Steps**:
1. Login as admin
2. Send DELETE to /api/users/99999
3. Verify response
**Expected**: 404 Not Found

### TC-USER-019: User List Pagination
**Priority**: Medium  
**Test Steps**:
1. Create 25+ users in database
2. Send GET to /api/users?page=2&per_page=10
3. Verify response structure
**Expected**: 200 OK with pagination metadata

### TC-USER-020: User List Filtering
**Priority**: Low  
**Test Steps**:
1. Send GET to /api/users?email=admin@test.com
2. Verify response
**Expected**: 200 OK with filtered results

### TC-USER-021: User List Sorting
**Priority**: Low  
**Test Steps**:
1. Send GET to /api/users?sort=name&order=desc
2. Verify response order
**Expected**: 200 OK with users sorted by name descending

### TC-USER-022: Update User Role - Admin
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Update regular user to admin role
3. Verify response and database
**Expected**: 200 OK, role updated successfully

### TC-USER-023: Update User Role - Regular User
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Attempt to change own role to admin
3. Verify response
**Expected**: 403 Forbidden or role unchanged

### TC-USER-024: Bulk User Operations - Not Supported
**Priority**: Low  
**Test Steps**:
1. Attempt bulk update/delete operations
2. Verify response
**Expected**: 405 Method Not Allowed or individual operation required

### TC-USER-025: User Profile Image Upload
**Priority**: Low  
**Test Steps**:
1. Login as user
2. Send PUT with profile image file
3. Verify upload and storage
**Expected**: 200 OK with image URL (if feature implemented)

---

## Product Management Module (20 Test Cases)

### TC-PROD-001: List All Products - Unauthenticated
**Priority**: High  
**Test Steps**:
1. Send GET to /api/products without authentication
2. Verify response
**Expected**: 200 OK with product list

### TC-PROD-002: List All Products - Authenticated
**Priority**: High  
**Test Steps**:
1. Send GET to /api/products with valid token
2. Verify response
**Expected**: 200 OK with product list

### TC-PROD-003: Get Product by ID - Valid Product
**Priority**: High  
**Test Steps**:
1. Send GET to /api/products/1
2. Verify response
**Expected**: 200 OK with product details

### TC-PROD-004: Get Product by ID - Non-existent Product
**Priority**: Medium  
**Test Steps**:
1. Send GET to /api/products/99999
2. Verify response
**Expected**: 404 Not Found

### TC-PROD-005: Create Product - Admin Role
**Priority**: High  
**Test Steps**:
1. Login as admin
2. Send POST to /api/products with valid product data
3. Verify response and database
**Expected**: 201 Created, product stored in database

### TC-PROD-006: Create Product - Regular User
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Attempt to create product
3. Verify response
**Expected**: 403 Forbidden

### TC-PROD-007: Create Product - Missing Required Fields
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Send POST with only product name
3. Verify response
**Expected**: 422 Validation error for missing fields

### TC-PROD-008: Create Product - Invalid Price
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Send POST with negative price
3. Verify response
**Expected**: 422 Validation error for invalid price

### TC-PROD-009: Create Product - Duplicate Product Name
**Priority**: Low  
**Test Steps**:
1. Create product "Test Product"
2. Attempt to create another product with same name
3. Verify response
**Expected**: 201 Created or 422 based on business rules

### TC-PROD-010: Update Product - Admin Role
**Priority**: High  
**Test Steps**:
1. Login as admin
2. Send PUT to /api/products/1 with update data
3. Verify response and database
**Expected**: 200 OK, product updated

### TC-PROD-011: Update Product - Regular User
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Attempt to update product
3. Verify response
**Expected**: 403 Forbidden

### TC-PROD-012: Update Product - Non-existent Product
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Send PUT to /api/products/99999
3. Verify response
**Expected**: 404 Not Found

### TC-PROD-013: Update Product - Partial Update
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Send PUT with only price field
3. Verify other fields unchanged
**Expected**: 200 OK, only price updated

### TC-PROD-014: Delete Product - Admin Role
**Priority**: High  
**Test Steps**:
1. Login as admin
2. Send DELETE to /api/products/1
3. Verify response and database
**Expected**: 200 OK, product deleted

### TC-PROD-015: Delete Product - Regular User
**Priority**: High  
**Test Steps**:
1. Login as regular user
2. Attempt to delete product
3. Verify response
**Expected**: 403 Forbidden

### TC-PROD-016: Delete Product - Non-existent Product
**Priority**: Low  
**Test Steps**:
1. Login as admin
2. Send DELETE to /api/products/99999
3. Verify response
**Expected**: 404 Not Found

### TC-PROD-017: Product List Pagination
**Priority**: Medium  
**Test Steps**:
1. Create 30+ products
2. Send GET to /api/products?page=2
3. Verify pagination structure
**Expected**: 200 OK with pagination metadata

### TC-PROD-018: Product Search/Filter
**Priority**: Low  
**Test Steps**:
1. Send GET to /api/products?search=test
2. Verify filtered results
**Expected**: 200 OK with relevant products

### TC-PROD-019: Product Sorting
**Priority**: Low  
**Test Steps**:
1. Send GET to /api/products?sort=price&order=asc
2. Verify sorted results
**Expected**: 200 OK with products sorted by price ascending

### TC-PROD-020: Product Out of Stock Scenario
**Priority**: Medium  
**Test Steps**:
1. Set product stock to 0
2. Verify product visibility in listings
3. Attempt to order out-of-stock product
**Expected**: Appropriate handling based on business rules

---

## Order Management Module (20 Test Cases)

### TC-ORDER-001: List User Orders - Authenticated User
**Priority**: High  
**Test Steps**:
1. Login as user with existing orders
2. Send GET to /api/orders
3. Verify response
**Expected**: 200 OK with user's orders

### TC-ORDER-002: List User Orders - No Orders
**Priority**: Medium  
**Test Steps**:
1. Login as new user without orders
2. Send GET to /api/orders
3. Verify response
**Expected**: 200 OK with empty array

### TC-ORDER-003: List User Orders - Unauthenticated
**Priority**: Medium  
**Test Steps**:
1. Send GET to /api/orders without token
2. Verify response
**Expected**: 401 Unauthorized

### TC-ORDER-004: Get Order by ID - Order Owner
**Priority**: High  
**Test Steps**:
1. Login as order owner
2. Send GET to /api/orders/{order_id}
3. Verify response
**Expected**: 200 OK with order details

### TC-ORDER-005: Get Order by ID - Different User
**Priority**: High  
**Test Steps**:
1. Login as user A
2. Attempt to get user B's order
3. Verify response
**Expected**: 403 Forbidden or 404 Not Found

### TC-ORDER-006: Get Order by ID - Admin User
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Get any user's order
3. Verify response
**Expected**: 200 OK with order details

### TC-ORDER-007: Get Order by ID - Non-existent Order
**Priority**: Low  
**Test Steps**:
1. Login as user
2. Send GET to /api/orders/99999
3. Verify response
**Expected**: 404 Not Found

### TC-ORDER-008: Create Order - Valid Data
**Priority**: High  
**Test Steps**:
1. Login as user
2. Send POST to /api/orders with valid product IDs and quantities
3. Verify response and database
**Expected**: 201 Created, order stored with correct total

### TC-ORDER-009: Create Order - Empty Order
**Priority**: Medium  
**Test Steps**:
1. Login as user
2. Send POST to /api/orders with empty items array
3. Verify response
**Expected**: 422 Validation error

### TC-ORDER-010: Create Order - Non-existent Product
**Priority**: Medium  
**Test Steps**:
1. Login as user
2. Send POST with non-existent product ID
3. Verify response
**Expected**: 422 Validation error or 404 for product

### TC-ORDER-011: Create Order - Insufficient Stock
**Priority**: High  
**Test Steps**:
1. Set product stock to 2
2. Attempt to order 5 units
3. Verify response
**Expected**: 422 Validation error for insufficient stock

### TC-ORDER-012: Create Order - Unauthenticated User
**Priority**: High  
**Test Steps**:
1. Send POST to /api/orders without token
2. Verify response
**Expected**: 401 Unauthorized

### TC-ORDER-013: Create Order - Multiple Products
**Priority**: Medium  
**Test Steps**:
1. Login as user
2. Send POST with multiple products and quantities
3. Verify order total and item count
**Expected**: 201 Created with correct calculations

### TC-ORDER-014: Update Order Status - Admin Role
**Priority**: High  
**Test Steps**:
1. Login as admin
2. Send PUT to /api/orders/{order_id} with status update
3. Verify response and database
**Expected**: 200 OK, status updated

### TC-ORDER-015: Update Order Status - Regular User
**Priority**: High  
**Test Steps**:
1. Login as order owner
2. Attempt to update order status
3. Verify response
**Expected**: 403 Forbidden

### TC-ORDER-016: Update Order Status - Invalid Status
**Priority**: Medium  
**Test Steps**:
1. Login as admin
2. Send PUT with invalid status value
3. Verify response
**Expected**: 422 Validation error

### TC-ORDER-017: Update Order Status - Non-existent Order
**Priority**: Low  
**Test Steps**:
1. Login as admin
2. Send PUT to /api/orders/99999
3. Verify response
**Expected**: 404 Not Found

### TC-ORDER-018: Order Total Calculation
**Priority**: High  
**Test Steps**:
1. Create order with products: (Product A: $10 x 2), (Product B: $15 x 1)
2. Verify order total calculation
**Expected**: Total = $35 (10*2 + 15*1)

### TC-ORDER-019: Order History Timestamp
**Priority**: Low  
**Test Steps**:
1. Create order
2. Verify created_at and updated_at timestamps
3. Update order status, verify updated_at changes
**Expected**: Proper timestamp management

### TC-ORDER-020: Order Cancellation Workflow
**Priority**: Medium  
**Test Steps**:
1. Create order
2. Cancel order (if cancellation feature exists)
3. Verify stock restoration and status update
**Expected**: Proper cancellation handling

---

## Edge Cases & Negative Scenarios (10 Test Cases)

### TC-EDGE-001: SQL Injection Attempt
**Priority**: High  
**Test Steps**:
1. Attempt SQL injection in search fields
2. Verify response and database safety
**Expected**: Proper sanitization, no database errors

### TC-EDGE-002: XSS Attack Attempt
**Priority**: High  
**Test Steps**:
1. Submit script tags in user input fields
2. Verify data storage and display
**Expected**: Proper escaping, no script execution

### TC-EDGE-003: Large Number Handling
**Priority**: Medium  
**Test Steps**:
1. Attempt to order very large quantities
2. Verify system handling
**Expected**: Proper validation or error handling

### TC-EDGE-004: Special Characters in Input
**Priority**: Low  
**Test Steps**:
1. Submit data with special characters
2. Verify proper storage and retrieval
**Expected**: Data handled correctly

### TC-EDGE-005: Concurrent Order Creation
**Priority**: Medium  
**Test Steps**:
1. Simulate multiple users ordering same low-stock product
2. Verify stock consistency
**Expected**: No overselling, proper locking

### TC-EDGE-006: Token Manipulation
**Priority**: High  
**Test Steps**:
1. Modify token and attempt access
2. Verify security response
**Expected**: 401 Unauthorized

### TC-EDGE-007: Rate Limiting
**Priority**: Medium  
**Test Steps**:
1. Send rapid consecutive requests
2. Verify rate limiting response
**Expected**: 429 Too Many Requests if implemented

### TC-EDGE-008: File Upload Size Limits
**Priority**: Low  
**Test Steps**:
1. Attempt to upload very large files
2. Verify response
**Expected**: Proper file size validation

### TC-EDGE-009: Database Connection Failure
**Priority**: Low  
**Test Steps**:
1. Simulate database downtime
2. Verify error handling
**Expected**: Graceful error response

### TC-EDGE-010: API Version Handling
**Priority**: Low  
**Test Steps**:
1. Access endpoints with different API versions
2. Verify version handling
**Expected**: Proper version support or error

---

## Test Data Requirements

### User Data
- Admin user: admin@test.com / password
- Regular users: user1@test.com, user2@test.com / password
- Test user for registration: various test emails

### Product Data
- Multiple products with different prices, stocks
- Out-of-stock products
- High-value products for total calculation

### Order Data
- Orders in different statuses
- Orders with multiple products
- Orders from different users

## Test Environment
- **API Base URL**: http://localhost:8000/api
- **Database**: MySQL 8.0+
- **PHP Version**: 8.1+
- **Laravel Version**: 10.x
- **Testing Tools**: Postman, PHPUnit, Browser DevTools

## Risk Assessment
- **High Risk**: Authentication bypass, data leakage
- **Medium Risk**: Business logic flaws, calculation errors  
- **Low Risk**: UI issues, minor validation problems

## Exit Criteria
- All critical test cases executed
- No open critical/high severity bugs
- 80%+ test case pass rate
- Performance benchmarks met
- Security review completed