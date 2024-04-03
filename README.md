<h1 align="center">Leo's random redirector :)</h1>
<p align="center">random link redirection with Base64URL + <a href="https://github.com/Le0X8/obf?tab=readme-ov-file#byte-difference">byte difference</a> obfuscation</p>

---

## About

This project can be used to redirect users to random destinations. The user can't guess the link target, because the URL is obfuscated and Base64URL-encoded.

### Why could this be useful?

- Some link visitors are going to be rickrolled, and some aren't. (ok that's not really useful tbh)

### How it works

#### [Server side](index.php)

1. Split the request URL. If the URL points to the root, `page.html` will be displayed.
2. [Recreate every URL](#obfuscation-process) for each part in the split URL.
3. Tell the browser the URL the page moved. If more than one URL is provided, the server will return HTTP status `307 Temporary Redirect`, otherwise it will return `301 Moved Permanently`.

#### [Client side](page.html)

1. Redirect to the URL the server points to, otherwise show an URL generator for [creating obfuscated URLs](#obfuscation-process).

#### Obfuscation process

To make it harder to guess the actual URL just by looking at it, it's obfuscated and encoded.

1. `https://`, `http://`, and `www.` are stripped away from the URL to make the obfuscated version shorter. These parts are added later in step 4.
2. The URL is obfuscated using the byte difference method; this method is documented [here](https://github.com/Le0X8/obf?tab=readme-ov-file#byte-difference).
3. The obfuscated URL is base64url-encoded, because the obfuscated URL would be binary, and URLs aren't allowing literal binary data.
4. Add the parts that have been stripped away in step 1 back, resulting in the following format: `. (if the protocol was unsecured http) + {obfuscated and encoded URL} + . (if the host started with www.)`.

**Example:**

URL: `https://leox.dev`

1. `leox.dev`
2. `[BINARY STUFF]`
3. `bPkKCbY2ARE`
4. `bPkKCbY2ARE`

This can also be tested in the JS console in the devtools on [`r.leox.li`](https://r.leox.li/):

```js
encode('https://leox.dev');
//> 'bPkKCbY2ARE'
```

## Self hosting

This project can be self-hosted. Currently only available for `apache2` webservers with activated PHP 8+ and `.htaccess` configuration enabled.

## License

This project is licensed under the [MIT License](LICENSE)
