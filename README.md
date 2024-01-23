# PHP Tailwind Dashbaord template

A template for quickly deploying apps (best suited for dashboard apps) that will run the most modern PHP version, has MySQL DB and authentication methods such as local and Microsoft Entra ID (Azure AD) out of the box and a lot of nice components such as DataGrid and Forms. Suitable for monolith or microservices. Has content-security-policy reporting endpoint, Firewall to filter traffic and much more. Ready for deployment anywhere. Supports out of the box color dark/light as well as color themes as well as user and admin panels. All written in pure PHP.

## Authentication

Supports local and Microsoft Entra ID (Azure AD) authentication both and utilizes a secure JWT implementation and verification with support classes.

### Microsoft Entra ID Authentication

This works by either creating an app registration in your Azure tenant (works with both single-tenant, multi-tenant both) and providing the client id and tenant id in the site-settings.php file or by deploying to an Azure App Service web app with Authentication enabled on the web app

#### Your own app registration

1. Create App Registration
2. In Authentication -> Put the redirect uri - https://example.com/auth-verify
3. In Authentication -> Enable ID Tokens
4. In Token Configuration -> Add optional claims - ctry, ipaddr
5. In App Roles -> Create app role -> display name => App Name Admins, allowed member types => Users/Groups, Value = administrator
6. To assign admins browse the app registration as Enterprise app -> Users and groups -> Add user/group -> Select users -> Role should be only the new admin role

#### Azure App Service Web app with Authentication enabled

The template will try to detect if this scenario and you will not need to manually create an app registration

### JWT Setup

The app issues JWT tokens to logged in users (Azure AD and local), so you will need to prepare a public and private key pair for the signing and validation. 

#### Creating the public and private keys

So to create yourself a pair of private and public keys use openssl.

`Generate a Private Key`

``` bash
openssl genpkey -algorithm RSA -out private-key.pem
```

`Generate a Public Key from the Private Key`

``` bash
openssl rsa -pubout -in private-key.pem -out public-key.pem
```

#### Providing them to the app

After you have created the public and private keys, you need to base64 encode them and provide them to the app. You can do this by either add them manually to the `.env` file or you can pass them as environmental variables. They are read in `./config/site-settings.php` in `JWT_PUBLIC_KEY` and `JWT_PRIVATE_KEY` constants. If you use Docker container, the DOCKERFILE already accoutns for them so you need to pass them as --build-arg when creating the image.

## Routing and available variables

Routing provides the following variables for each controller

**GET** requests

``$usernameArray`` - An array holding user info

``$isAdmin`` - A boolean if the user is an admin

**POST**, **PUT**, **DELETE** and other REST methods

``$vars`` - An array holding 4 keys

**usernameArray** (An array holding user info)

**isAdmin** (A boolean if the user is an admin)

**loggedIn** (A boolean if the user is an admin)

**theme** (a string holind the user theme)

## Forms

If you want to create a form.

``` php
use Template\Forms;
```

Then you need to create an options array

``` php
$formOptions = [
    // If you need input fields, open an 'inputs' key
    'inputs' => [
        'input' => [
            [
                'label' => 'Email Address',
                'type' => 'email',
                'placeholder' => 'John.Doe@example.com',
                'name' => 'email',
                'description' => 'Provide a valid email',
                'id' => uniqid(),
            ]
        ]
    ],
    'hidden' => [
        [
            'name' => 'username',
            'value' => $usernameArray['username']
        ]
    ],
    // Now come the form options and the submit button options
    'theme' => $theme, // Optional, defaults to COLOR_SCHEME
    'method' => 'POST', // Optional, defaults to POST
    'action' => '/api/example', // Required
    'additionalClasses' => 'qwerty power', // Optional
    'reloadOnSubmit' => false,
    //'confirm' => true,
    //'confirmText' => 'Are you sure you want to send this quack?', // Optional, defaults to "Are you sure?" if ommited
    'resultType' => 'text',
    //'doubleConfirm' => true,
    //'doubleConfirmKeyWord' => 'delete',
    'submitButton' => [
        'text' => 'Submit',
        'id' => uniqid(),
        'name' => 'submit',
        'type' => 'submit',
        'size' => 'medium',
        'disabled' => true,
        'title' => 'Disabled'
        //'style' => '&#10060;'
    ]
];
```

### Options

Here are the options that are accepted for Forms.

#### Form Options

These form options are presneted into the options array

| Option   | Type     | Required | Default | Description           |
|----------|----------|----------|---------|-----------------------|
| inputs  | Array   | Yes      | -       | This is the main holder for ```<input>``` type of fields|
| theme  | string   | No       | COLOR_SCHEME      | This sets the theme color for the form. You could set it to the current user theme by giving it value of ```$theme```|
| method  | string     | No      | POST  | ```GET```, ```POST```, ```PUT```, ```DELETE```|
| action  | string     | Yes      | -  | Fetch URI |
| additionalClasses  | string     | No      | -  | String comprised of additional classes to be added to the ```<form>```. Syntax is comma-separated classes such as ``` 'customClass, customClass2' ``` |
| reloadOnSubmit  | bool     | No      | -  | Should the page refresh after the form has successfully submitted and received a HTTP Status OK. If status code is >= 300, it will not refresh |
| redirectOnSubmit  | string     | No      | -  | URL where we redirect after a successful form submission |
| confirm  | bool     | No      | -  | If the form will present a Modal before submitting the form |
| confirmText  | string     | No      | 'Are you sure?'  | What text should appear on the modal. It can support text or html. Needs ```confirm``` to be set and be ```true``` |
| doubleConfirm  | bool     | No      | -  | If this is set and set to ```true``` the form will present a special Modal that would require a special ```keyword``` to be provided before the Submit button is enabled |
| doubleConfirmKeyWord  | string     | No      | -  | Provides the ```keyword``` to be put if ```doubleConfirm``` is set to ```true``` |
| resultType | string | No | ' | Values of ```html``` or ```text``` only. If ```html```, the result will be displayed as ```.innerHTML```, otherwise as ```.innerText``` in the result div |
| submitButton | Array | Yes | - | Array of settings for the submit Button of the form |
