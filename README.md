## Laravel Starter Kit

is an ultra-strict, type-safe [Laravel](https://laravel.com) skeleton engineered for developers who refuse to compromise on code quality. This opinionated starter kit enforces rigorous development standards through meticulous tooling configuration and architectural decisions that prioritize type safety, immutability, and fail-fast principles.

## Why This Starter Kit?

Modern PHP has evolved into a mature, type-safe language, yet many Laravel projects still operate with loose conventions and optional typing. This starter kit changes that paradigm by enforcing:

- **100% Type Coverage**: Every method, property, and parameter is explicitly typed
- **Zero Tolerance for Code Smells**: Rector and PHPStan at maximum strictness catch issues before they become bugs
- **Immutable-First Architecture**: Data structures favor immutability to prevent unexpected mutations
- **Fail-Fast Philosophy**: Errors are caught at compile-time, not runtime
- **Automated Code Quality**: Pre-configured tools ensure consistent, pristine code across your entire team
- **Just Better Laravel Defaults**: Thanks to **[Essentials](https://github.com/nunomaduro/essentials)** / strict models, auto eager loading, immutable dates, and more...

This isn't just another Laravel boilerplate—it's a statement that PHP applications can and should be built with the same rigor as strongly-typed languages like Rust or TypeScript.

## Getting Started

> **Requires [PHP 8.4+](https://php.net/releases/)**.

Create your type-safe Laravel application using :

```bash
git clone https://github.com/Kawarem-Co/laravel-starter-kit
```

### Initial Setup

Navigate to your project and complete the setup:

```bash
cd example-app

# Setup project
composer setup

# Start the development server
composer dev
```

### Verify Installation

Run the test suite to ensure everything is configured correctly:

```bash
composer test
```

You should see 100% test coverage and all quality checks passing.

## Available Tooling

### Development
- `composer dev` - Starts Laravel server, queue worker, log monitoring, and Vite dev server concurrently

### Code Quality
- `composer lint` - Runs Rector (refactoring), Pint (PHP formatting), and Prettier (JS/TS formatting)
- `composer test:lint` - Dry-run mode for CI/CD pipelines

### Testing
- `composer test:type-coverage` - Ensures 100% type coverage with Pest
- `composer test:types` - Runs PHPStan at level 9 (maximum strictness)
- `composer test:unit` - Runs Pest tests with 100% code coverage requirement
- `composer test` - Runs the complete test suite (type coverage, unit tests, linting, static analysis)

### Maintenance
- `composer update:requirements` - Updates all PHP and NPM dependencies to latest versions

## Auto-CRUD Generation

This project includes an auto-CRUD generation system that can automatically create controllers, services, requests, resources, and filter builders for your models.

### Basic Usage

```bash
php artisan auto-crud:generate --model=User
```

### Available Options

- `--service` or `-S` - Generate with service layer (no repository pattern)
- `--filter` or `-F` - Generate filter builder and filter request for advanced filtering
- `--pattern=spatie-data` or `-P=spatie-data` - Use Spatie Laravel Data pattern (requires FormRequest + Data class)
- `--type=api` or `-T=api` - Generate API controller (default: api)
- `--type=web` or `-T=web` - Generate web controller
- `--type=both` or `-T=both` - Generate both API and web controllers
- `--overwrite` or `-O` - Overwrite existing files
- `--model=ModelName` or `-M=ModelName` - Specify model(s) to generate CRUD for
- `--model-path=path` or `-MP=path` - Set custom models path

### Examples

**Generate CRUD with service layer and Spatie Data pattern:**
```bash
php artisan auto-crud:generate --service --pattern=spatie-data --model=User
```

**Generate CRUD with filter builder:**
```bash
php artisan auto-crud:generate --service --pattern=spatie-data --filter --model=User
```

**Generate with all options:**
```bash
php artisan auto-crud:generate --service --pattern=spatie-data --filter --model=User --overwrite
```

### Generated Files

When using `--service --pattern=spatie-data --filter`, the command generates:

1. **UserService** (`app/Services/UserService.php`)
   - Contains `store()` and `update()` methods only
   - Uses `DB::transaction()` for data safety
   - Handles media uploads automatically if model uses `HasMediaConversions` trait
   - Accepts `UserData` objects instead of arrays

2. **UserRequest** (`app/Http/Requests/UserRequest.php`)
   - FormRequest for validation
   - Properties in camelCase
   - Includes media validation rules automatically

3. **UserFilterRequest** (`app/Http/Requests/UserFilterRequest.php`) - *when using `--filter`*
   - Validation for `search` and `perPage` parameters

4. **UserFilterBuilder** (`app/FilterBuilders/UserFilterBuilder.php`) - *when using `--filter`*
   - Extends `BaseFilterBuilder`
   - Includes `textSearch()` method if model has searchable properties
   - Used in controller's `index()` method

5. **UserData** (`app/Data/UserData.php`)
   - Spatie Laravel Data class
   - Includes `HasModelAttributes` trait
   - Automatically includes media properties if model uses media trait

6. **UserResource** (`app/Http/Resources/UserResource.php`)
   - API Resource for responses
   - Includes `@mixin` PHPDoc tag
   - Always includes `id`, `createdAt`, `updatedAt`
   - Automatically handles media collections
   - Properties in camelCase

7. **UserController** (`app/Http/Controllers/API/UserController.php`)
   - No base controller extension
   - No try-catch blocks (Laravel handles exceptions)
   - Uses Resources for responses
   - Index method uses FilterBuilder when `--filter` is enabled
   - Loads media relations automatically
   - Destroy returns `response()->noContent()` (204 status)

### Features

- **Automatic Media Detection**: Detects media fields from model traits and generates appropriate code
- **Type Safety**: Uses Data classes with proper type hints
- **Transaction Safety**: All store/update operations wrapped in DB transactions
- **CamelCase Properties**: All request and resource properties use camelCase
- **Media Support**: Automatically handles single and multiple file uploads
- **Filter Support**: Advanced filtering with search and pagination

## Documentation

| File | Description |
|------|-------------|
| [docs/api/medical-record-api-contract.md](docs/api/medical-record-api-contract.md) | API contract for the Medical Record feature (Flutter / frontend integration) |
| [docs/api/booking-api-contract.md](docs/api/booking-api-contract.md) | API contract for the Booking (Calendar / Appointments) feature (Flutter / frontend integration) |
| [docs/api/patient-management-api-contract.md](docs/api/patient-management-api-contract.md) | API contract for the Patient Management feature (Flutter / frontend integration) |
| [docs/api/user-management-api-contract.md](docs/api/user-management-api-contract.md) | API contract for the User Management feature (Flutter / frontend integration) |
| [docs/api/chronic-diseases-api-contract.md](docs/api/chronic-diseases-api-contract.md) | API contract for the Chronic Diseases feature (Flutter / frontend integration) |
| [docs/api/chronic-medications-api-contract.md](docs/api/chronic-medications-api-contract.md) | API contract for the Chronic Medications feature (Flutter / frontend integration) |
| [docs/api/dental-labs-api-contract.md](docs/api/dental-labs-api-contract.md) | API contract for the Dental Labs feature (Flutter / frontend integration) |
| [docs/api/treatments-api-contract.md](docs/api/treatments-api-contract.md) | API contract for the Treatments feature (Flutter / frontend integration) |
| [docs/api/filler-materials-api-contract.md](docs/api/filler-materials-api-contract.md) | API contract for the Filler Materials feature (Flutter / frontend integration) |
| [CLAUDE.md](CLAUDE.md) | Project and Laravel Boost guidelines for AI assistants |
| [TEST_ISSUES_REPORT.md](TEST_ISSUES_REPORT.md) | Test issues and notes |

## License
**Laravel Starter Kit** was created by **[Mustafa Fares](https://www.linkedin.com/in/mustafa-fares/)** Forking from **[Nuno Maduro](https://x.com/enunomaduro)** Package.
