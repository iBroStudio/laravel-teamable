# Laravel Teamable

Add team properties to Eloquent models.

## Installation

Install the package via composer:

```bash
composer require ibrostudio/laravel-teamable
```

Then run the installer:

```bash
php artisan data-repository:install
```

## Usage

### Configuration

Add the trait `IBroStudio\Teamable\Concerns\IsTeamable` and the interface `\IBroStudio\Teamable\Contracts\Teamable` to the Eloquent model which must be a team.

**You can define many models as a team according to your needs.**

```php
namespace App\Models;

use IBroStudio\Teamable\Concerns\IsTeamable;
use Illuminate\Database\Eloquent\Model;

class Company extends Model implements \IBroStudio\Teamable\Contracts\Teamable
{
    use IsTeamable;
}
```

Add the trait `IBroStudio\Teamable\Concerns\HasTeams` to the Eloquent model that will define member of the team.

```php
namespace App\Models;

use IBroStudio\Teamable\Concerns\HasTeams;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasTeams;
}
```

### Creating team

When you create a model that has the `IBroStudio\Teamable\Concerns\IsTeamable` trait, a `IBroStudio\Teamable\Models\Team` model is automatically created using the model's property defined in the `teamable.php` config file as the team name.

The default model property used is `name`. If your model does not have name, you can set another property to use in the `teamable.php` configuration file: 

```php
'model_name_attribute' => [
    'default' => 'name',
    Company::class => 'company_name'
],
```

You can disable automatic team creation by setting `auto.create` to false in the `teamable.php` configuration file.

```php
$company = Company::create([
    'name' => 'iBroStudio',
]);

$company->team; // This give access to the team model through a MorphOne relationship
```

To add team to an existing model:

```php
$company->createTeam();

$company->team; // This give access to the team model
```

### Add member to a team

```php
$user->attachTeam($company->team);
```

A user can be a member of multiple teams and different team types:

```php
$user->attachTeam($company_1->team);
$user->attachTeam($company_2->team);

$department = Department::create([
    'name' => 'Development',
]);
$user->attachTeam($department->team);
```

### Current team ID

For ease of use, a **current team ID** property is stored in a data repository for each team type.

When you add a member to a team, the value is set to that team's ID.

You can retrieve the value with the `getCurrentTeamId(TeamType $teamType)`method.

```php
$user->getCurrentTeamId(TeamType::make(Company::class));
// or $user->getCurrentTeamId($company_1->team->type);

$user->getCurrentTeamId(TeamType::make(Department::class));
```

To change the current team ID, use the `switchToTeam` method:

```php
$user->switchToTeam($company_2->team);
```

### Remove member from a team

```php
$user->detachTeam($company->team);
```

### Deleting team

Team model is automatically deleted when you delete the model that has the `IBroStudio\Teamable\Concerns\IsTeamable` trait.

You can disable automatic team deletion by setting `auto.delete` to false in the `teamable.php` configuration file.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
