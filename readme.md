# Caesar Cipher Decryption

Written in PHP this will attempt to decrypt a Caesar Cipher by using a dictionary and letter frequencies/letter mappings as its source to decrypt. If you provide a key, each word from that key is added to the dictionary.

The main file used for decryption is located in `app/Classes/Decrypt.php`

For minimal setup, you can run this via the command line running:

`php artisan cipher:decrypt --file={relative to storage/app folder} --keyFile={relative to storage/app folder}`

If you use the online GUI, you can directly enter the text into textarea fields.

Features:
- Can Decrypt with 95% of letters matching to create a key
- From the generated key the command line or the online gui will display the encrypted text
- Using sockets with Redis to display progress and letter matches as they occur.
- Uses Vue.js on the front-end

# Setup

Setup:

> composer install
> npm install
> npm run dev/prod
> Ensure Redis is setup
> Rename env.prod to .env
> Change the `REDIS_PASSWORD` to your password

##### Then to avoid server setup run in seperate terminal windows:
1. > php artisan queue:work
2. > laravel-echo-server start
3. > php artisan serve
