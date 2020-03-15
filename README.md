# XTND CMS
A simple content management system for laravel 

# Installation
Install the package via composer using "composer require xtnd/cms" and follow the 5 simple steps below.

# Setup

# Step 1

Add to the autoload/psr-4 object in the composer.json file in your project's root the following:

"Xtnd\\\Cms\\\\": "vendor/xtnd/cms/src"

# Step 2
Add to the providers array in config/app.php the following:

Xtnd\Cms\CmsServiceProvider::class

# Step 3
Add to the guards array in config/auth.php the following:

'cms_user' => [
            'driver' => 'session',
            'provider' => 'cms_users'
        ]
        
and to the providers array the following:

'cms_users' => [
            'driver' => 'eloquent',
            'model' => Xtnd\Cms\CmsUser::class
        ]
        
# Step 4
Run the following commands in terminal:

composer dump-autoload

php artisan vendor:publish --tag=public --force

php artisan migrate

# Step 5

In app/exceptions/handler.php:

Add to the top the following:

use Illuminate\Auth\AuthenticationException;

And add to the class the following function to redirect unauthenticated routes to the login page:


    protected function unauthenticated($request, AuthenticationException $exception){   

        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }



        $guard = array_get($exception->guards(),0);
        switch ($guard) {
            case 'cms_user':
                $login = 'cmslogin';
                break;

            default:
                $login = 'login';
                break;
        }

        return redirect()->guest(route($login));
    }


# Step 6
To create your first CMS user, go to the following route:

/cms/register

And voila! You're all set.

# IMPORTANT:
every time you add a table or column, you need to run these 2 routes while being logged in, to configure the cms:

/cms/configure

/cms/configureTables

This step wil be made easier in future releases


