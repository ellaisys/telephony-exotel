# Laravel Package for Exotel Telephony
Cloud Telephony PHP/Laravel based package for providing Exotel Services

![Latest Version on Packagist](https://img.shields.io/packagist/v/ellaisys/telephony-exotel?style=flat-square)
![Release Date](https://img.shields.io/github/release-date/ellaisys/telephony-exotel?style=flat-square)
![Total Downloads](https://img.shields.io/packagist/dt/ellaisys/telephony-exotel?style=flat-square)
![APM](https://img.shields.io/packagist/l/ellaisys/telephony-exotel?style=flat-square)


This package provides a simple way to use AWS Cognito authentication in Laravel 7.x for Web and API Auth Drivers.
We decided to use it and contribute it to the community as a package, that encourages standarised use and a RAD tool for authentication using AWS Cognito. 

Currently we have the following features implemented in our package:

- Registration and Confirmation E-Mail
- Login
- Remember Me Cookie

### Disclaimer
_This package is currently in development and is not production ready._

## Installation

You can install the package via composer.

```bash
composer require ellaisys/telephony-exotel
```

#### Laravel 5.4 and before
Using a version prior to Laravel 5.5 you need to manually register the service provider.

```php
// config/app.php
'providers' => [
    ...
    Ellaisys\Exotel\Providers\ExotelServiceProvider::class,
    
];
```

Next you can publish the config and the view.

```bash
php artisan vendor:publish --provider="Ellaisys\Exotel\Providers\ExotelServiceProvider"
```

## Cognito User Pool

In order to use AWS Cognito as authentication provider, you require a Cognito User Pool. 

If you haven't created one already, go to your [Amazon management console](https://console.aws.amazon.com/cognito/home) and create a new user pool. 

Next, generate an App Client. This will give you the App client id and the App client secret
you need for your `.env` file. 

*IMPORTANT: Don't forget to activate the checkbox to Enable sign-in API for server-based Authentication. 
The Auth Flow is called: ADMIN_USER_PASSWORD_AUTH (formerly ADMIN_NO_SRP_AUTH)*

You also need a new IAM Role with the following Access Rights:

- AmazonCognitoDeveloperAuthenticatedIdentities
- AmazonCognitoPowerUser
- AmazonESCognitoAccess

From this user you can fetch the AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY.

### Exotel Telephony configuration

Add the following fields to your `.env` file and set the values according to your exotel settings:

```
# AWS configurations for cloud storage
AWS_ACCESS_KEY_ID="Axxxxxxxxxxxxxxxxxxxxxxxx6"
AWS_SECRET_ACCESS_KEY="mxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx+"

# AWS Cognito configurations
AWS_COGNITO_CLIENT_ID="6xxxxxxxxxxxxxxxxxxxxxxxxr"
AWS_COGNITO_CLIENT_SECRET="1xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx1"
AWS_COGNITO_USER_POOL_ID="xxxxxxxxxxxxxxxxx"
AWS_COGNITO_REGION="xxxxxxxxxxx" //optional - default value is 'us-east-1'
AWS_COGNITO_VERSION="latest" //optional - default value is 'latest'
```
For more details on how to find AWS_COGNITO_CLIENT_ID, AWS_COGNITO_CLIENT_SECRET and AWS_COGNITO_USER_POOL_ID for your application, please refer [COGNITOCONFIG File](COGNITOCONFIG.md)

### Importing existing users into the Cognito Pool

If you are already working on an existing project and want to integrate Cognito you have to [import a user csv file to your Cognito Pool](https://docs.aws.amazon.com/cognito/latest/developerguide/cognito-user-pools-using-import-tool.html).

## Usage

Our package is providing you 5 traits you can just add to your Auth Controllers to get our package running.

- Ellaisys\Cognito\Auth\AuthenticatesUsers
- Ellaisys\Cognito\Auth\RegistersUsers
- Ellaisys\Cognito\Auth\ResetsPasswords
- Ellaisys\Cognito\Auth\SendsPasswordResetEmails
- Ellaisys\Cognito\Auth\VerifiesEmails


In the simplest way you just go through your Auth Controllers and change namespaces from the traits which are currently implemented from Laravel.

You can change structure to suit your needs. Please be aware of the @extend statement in the blade file to fit into your project structure. 
At the current state you need to have those 4 form fields defined in here. Those are `token`, `email`, `password`, `password_confirmation`. 

## Single Sign-On

With our package and AWS Cognito we provide you a simple way to use Single Sign-Ons. 
For configuration options take a look at the config [cognito.php](/config/cognito.php).


When you want SSO enabled and a user tries to login into your application, the package checks if the user exists in your AWS Cognito pool. If the user exists, he will be created automatically in your database provided the `add_missing_local_user_sso` is to `true`, and is logged in simultaneously.

That's what we use the fields `sso_user_model` and `cognito_user_fields` for. In `sso_user_model` you define the class of your user model. In most cases this will simply be _App\User_. 

With `cognito_user_fields` you can define the fields which should be stored in Cognito. Put attention here. If you define a field which you do not send with the Register Request this will throw you an InvalidUserFieldException and you won't be able to register. 

Now that you have registered your users with their attributes in the AWS Cognito pool and your database and you want to attach a second app which should use the same pool. Well, that's actually pretty easy. You can use the API provisions that allows multiple projects to consume the same AWS Cognito pool. 

*IMPORTANT: if your users table has a password field you are not going to need this anymore. What you want to do is set this field to be nullable, so that users can be created without passwords. From now on, Passwords are stored in Cognito.

Any additional registration data you have, for example `firstname`, `lastname` needs to be added in 
[cognito.php](/config/cognito.php) cognito_user_fields config to be pushed to Cognito. Otherwise they are only stored locally 
and are not available if you want to use Single Sign On's.*


## Middleware configuration for API Routes
In case you are using this library as API driver, you can register the middleware into the kernal.php in the $routeMiddleware

    ```
    protected $routeMiddleware = [
        ...
        'aws-cognito' => \Ellaisys\Cognito\Http\Middleware\AwsCognitoAuthenticate::class
    ]
    ```

To use the middleware into the routes, as shown below

    ```
    Route::middleware('aws-cognito')->get('user', 'NameOfTheController@functionName');
    ```

In case you are using API authentication guards, make sure to add the authorization token into the request header.
To ensure that the application gets the request header data, please add the below line into the htaccess [.htaccess](/public/.htaccess) file

    ```
    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    ```

## Registering Users 

As a default, if you are registering a new user with Cognito, Cognito will send you an email during signUp were the user can verify themselves. 
If the user now clicks on the link in the email he will be redirected to a confirmation page which is provided by Cognito. 
In most cases, this is not you what you want. You want the user to stay on your page. 

We have found a neat way to get around this default behaviour. 

1. You need to create an extra field for the user where you want to store the verification token. This field has to be nullable.
2. Create an Event Listener that listens for Registered Event which is fired after the user has been registered. 
3. In this event listener, you generate a token and store that in the field you created above. 
4. You create an email and send that token, stored in a link, to the user.  
5. The link should point to a controller action where you first check if a user with this token exists. 
If such a exists in the database you make a call to Cognito and set the user Attributes to email_verified true and confirm the signUp.

    ```
     public function verifyEmail(
            $token,
            CognitoClient $cognitoClient,
            CognitoUserPropertyAccessor $cognitoUserPropertyAccessor
        ) {
            $user = User::whereToken($token)->firstOrFail();
    
            $user->token = null;
            $user->save();
    
            $cognitoClient->setUserAttributes($user->email, [
                'email_verified' => 'true',
            ]);
    
            if ($cognitoUserPropertyAccessor->getUserStatus($user->email) != 'CONFIRMED') {
                $cognitoClient->confirmSignUp($user->email);
                return response()->redirectToRoute('login');
            }
    
            return response()->redirectToRoute('dashboard');
        }
    ```

6. Now you need to turn off Cognito to send you emails. Go into your AWS account and navigate to the Cognito section. 
Select your user pool and click on `MFA and verifications`  You will see a headline: 
`Do you want to require verification of emails or phone numbers?`
You have to remove all checked fields here. Once done, you should see a red alert:
 `You have not selected either email or phone number verification, so your users will not be able to recover their passwords without contacting you for support.`

7. Now you have told Cognito to stop sending you messages when a user registers on your app and you can handle it all by yourself.

As a sidenote: Password Forgot Emails will still be triggered through Cognito. You cannot turn them off, so make sure to style those emails
to suit your needs. Also make sure to send the email from a proper FROM address. 

## Delete User

If you want to give your users the ability to delete themselves from your app you can use our deleteUser function
from the CognitoClient. 

To delete the user you should call deleteUser and pass the email of the user as a parameter to it.
After the user has been deleted in your cognito pool, delete your user from your database too.

```
    $cognitoClient->deleteUser($user->email);
    $user->delete();
```

We have implemented a new config option `delete_user`, which you can access through `AWS_COGNITO_DELETE_USER` env var. 
If you set this config to true, the user is deleted in the Cognito pool. If it is set to false, it will stay registered. 
Per default this option is set to false. If you want this behaviour you should set USE_SSO to true to let the user 
restore themselves after a successful login.

To access our CognitoClient you can simply pass it as a parameter to your Controller Action where you want to perform 
the deletion. 

```
    public function deleteUser(Request $request, AwsCognitoClient $client)
```

Laravel will take care of the dependency injection by itself. 

```
    IMPORTANT: You want to secure this action by maybe security questions, a second delete password or by confirming 
    the email address.
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Security

If you discover any security related issues, please email [support@ellaisys.com](mailto:support@ellaisys.com) and also add it to the issue tracker.

## Credits

- [EllaiSys Team](https://github.com/ellaisys)
- [Amit Dhongde](https://github.com/amitdhongde)

## Support us

EllaiSys is a web and consulting agency specialized in Cloud Computing (AWS and Azure), DevOps, and Product Engneering. We specialize into LAMP and Microsoft stack development. You'll find an overview of what we do [on our website](https://ellaisys.com).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
