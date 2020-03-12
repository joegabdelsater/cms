# cms
Content mangement system

# Step 1
Install the package via composer using "composer require xtnd/cms"

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

# Step 5
Run the following command in terminal 

php artisan migrate

And voila!
