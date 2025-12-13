# Authentication System with Roles and Permissions

## Overview

This Laravel application includes a complete authentication system with role-based access control (RBAC) using:
- **Laravel Breeze** - For authentication scaffolding
- **Spatie Laravel Permission** - For roles and permissions management

## Installation & Setup

### 1. Run Migrations

First, make sure your database is created and MySQL is running in XAMPP:

```bash
php artisan migrate
```

This will create all necessary tables including:
- `users` table
- `roles` table
- `permissions` table
- `model_has_roles` table (pivot)
- `model_has_permissions` table (pivot)
- `role_has_permissions` table (pivot)

### 2. Seed Roles and Permissions

Run the seeder to create default roles and permissions:

```bash
php artisan db:seed
```

Or specifically:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 3. Default Users Created

The seeder creates the following test users:

| Email | Password | Role |
|-------|----------|------|
| admin@textile.com | password | Super Admin |
| manager@textile.com | password | Manager |
| staff@textile.com | password | Staff |

## Roles and Permissions

### Roles

1. **Super Admin** - Full access to everything
2. **Admin** - Most permissions except role management
3. **Manager** - View and edit permissions for operations
4. **Staff** - Limited permissions for daily operations
5. **User** - Basic viewing permissions

### Permissions

The system includes permissions for:

- **User Management**: view, create, edit, delete users
- **Role & Permission Management**: view, create, edit, delete, assign roles
- **Product Management**: view, create, edit, delete products
- **Order Management**: view, create, edit, delete, process orders
- **Inventory Management**: view, create, edit, delete, manage stock
- **Supplier Management**: view, create, edit, delete suppliers
- **Customer Management**: view, create, edit, delete customers
- **Reports**: view, export reports
- **Settings**: view, edit settings

## Usage Examples

### In Controllers

```php
// Check if user has a role
if (auth()->user()->hasRole('Admin')) {
    // Do something
}

// Check if user has a permission
if (auth()->user()->can('view products')) {
    // Do something
}

// Assign role to user
$user->assignRole('Manager');

// Give permission to user
$user->givePermissionTo('edit products');

// Give permission to role
$role = Role::findByName('Manager');
$role->givePermissionTo('view products');
```

### In Routes

```php
// Protect route with role
Route::middleware(['role:Admin|Super Admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Protect route with permission
Route::middleware(['permission:view products'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});
```

### In Blade Templates

```blade
{{-- Check role --}}
@hasrole('Admin')
    <p>Admin content</p>
@endhasrole

{{-- Check permission --}}
@can('view products')
    <a href="{{ route('products.index') }}">Products</a>
@endcan

{{-- Check multiple roles --}}
@hasanyrole('Admin|Super Admin')
    <p>Admin or Super Admin content</p>
@endhasanyrole
```

## Middleware

Spatie Permission provides middleware that you can use:

- `role:Admin` - User must have the Admin role
- `permission:view products` - User must have the permission
- `role_or_permission:Admin|view products` - User must have role OR permission

## Managing Roles and Permissions

### Create a Role

```php
use Spatie\Permission\Models\Role;

$role = Role::create(['name' => 'Editor']);
```

### Create a Permission

```php
use Spatie\Permission\Models\Permission;

$permission = Permission::create(['name' => 'edit articles']);
```

### Assign Role to User

```php
$user->assignRole('Editor');
// or
$user->assignRole(['Editor', 'Writer']);
```

### Give Permission to Role

```php
$role = Role::findByName('Editor');
$role->givePermissionTo('edit articles');
```

### Revoke Permission

```php
$user->revokePermissionTo('edit articles');
$role->revokePermissionTo('edit articles');
```

## Testing

To test the authentication system:

1. Start the development server:
   ```bash
   php artisan serve
   ```

2. Visit: http://localhost:8000

3. Login with one of the test accounts:
   - Email: `admin@textile.com`
   - Password: `password`

4. Check the dashboard to see your roles and permissions

5. Try accessing different routes based on your role/permissions

## Security Notes

- Always use middleware to protect routes
- Use `@can` and `@hasrole` directives in Blade templates
- Never trust client-side checks alone - always verify on the server
- Regularly review and update roles and permissions
- Use the principle of least privilege

## Additional Resources

- [Laravel Breeze Documentation](https://laravel.com/docs/breeze)
- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)

