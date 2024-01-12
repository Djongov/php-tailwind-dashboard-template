# PHP Tailwind Dashbaord template

## Azure AD Authentication

1. Create App Registration
2. In Authentication -> Put the redirect uri - https://example.com/auth-verify
3. In Authentication -> Enable ID Tokens
4. In Token Configuration -> Add optional claims - ctry, ipaddr
5. In App Roles -> Create app role -> display name => App Name Admins, allowed member types => Users/Groups, Value = administrator
6. To assign admins browse the app registration as Enterprise app -> Users and groups -> Add user/group -> Select users -> Role should be only the new admin role

## Private and Public Key for JWT validation and creation

The app issues JWT tokens to logged in users, so to create yourself a pair of private and public keys use this

`Generate a Private Key`

``` bash
openssl genpkey -algorithm RSA -out private-key.pem
```

`Generate a Public Key from the Private Key`

``` bash
openssl rsa -pubout -in private-key.pem -out public-key.pem
```

You should take care of the private key security yourself once you generate it. You either pull it from somewhere or place it in an environmental variable. One option is to base64 encode it and put in the .env file

You then point `JWT_PUBLIC_KEY` and `JWT_PRIVATE_KEY` in ./config/site-settings.php to the location of these keys
