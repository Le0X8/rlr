<script lang="ts">
	import '../styles/global.scss';
	import { onMount } from 'svelte';
	import { Buffer } from 'buffer/';

	onMount(() => {
		document.body.classList.remove('nojs');

		const url = document.getElementById('url') as HTMLInputElement;
		const urlOut = document.getElementById('out') as HTMLInputElement;
		const urls = document.getElementById('urls') as HTMLUListElement;
		const add = document.getElementById('add') as HTMLButtonElement;
		const create = document.getElementById('create') as HTMLButtonElement;
		const copy = document.getElementById('copy') as HTMLButtonElement;
		const addedUrls: string[] = [];

		const obf = (input: Buffer) => {
			let prev = 0;
			const out = Buffer.alloc(input.byteLength);

			for (let i = 0; i < input.byteLength; i++) {
				const val =
					input.readUInt8(i) < prev ? input.readUInt8(i) - prev + 256 : input.readUInt8(i) - prev;
				prev += val;
				prev %= 256;
				out.writeUInt8(val, i);
			}

			return out;
		};

		const b64u = (input: string) => {
			return Buffer.from(input).toString('base64').replaceAll('+', '-').replaceAll('/', '_').replaceAll('=', '');
		};

		const encode = (input: string) => {
			const https = input.charAt(4) == 's';
			let realUrl = input.slice(7 + +https);
			const www = realUrl.startsWith('www.');
			if (www) realUrl = realUrl.slice(4);
			return (https ? '' : '.') + b64u(obf(Buffer.from(realUrl, 'utf8')).toString('utf8')) + (www ? '.' : '');
		};

		add.addEventListener('click', (ev) => {
			addedUrls.push(url.value);
			const el = document.createElement('li');
			el.innerText = url.value;
			urls.appendChild(el);
			url.value = '';
		});

		create.addEventListener('click', (ev) => {
			urlOut.value =
				window.location.protocol +
				'//' +
				window.location.host +
				'/' +
				addedUrls.map((u) => encode(u)).join('/');
			addedUrls.forEach(() => addedUrls.pop());
			urls.textContent = '';
		});

		copy.addEventListener('click', (ev) => {
			copy.disabled = true;
			urlOut.select();
			urlOut.setSelectionRange(0, 99999);
			navigator.clipboard.writeText(urlOut.value);
			copy.innerText = 'Copied!';
			setTimeout(() => {
				copy.innerText = 'Copy';
				copy.disabled = false;
			}, 1000);
		});

		window.encode = encode;
	});
</script>

<nav>
	Switch to
	<a href="https://r.leox.dev/">HTTPS</a>
	<a href="http://r.leox.dev/">HTTP</a>
</nav>
<hgroup>
	<h1>Leo's random redirector :)</h1>
	<h2>
		random link redirection with Base64URL + <a
			href="https://github.com/Le0X8/obf?tab=readme-ov-file#byte-difference"
			target="_blank">byte difference</a
		> obfuscation
	</h2>
</hgroup>
<noscript> This link generator needs JavaScript to work, so you should activate it! </noscript>
<main>
	<h3>Add an URL:</h3>
	<span>
		<input type="url" value="" placeholder="https://leox.dev/" id="url" />
		<button id="add">Add URL!</button>
	</span>

	<h3>Added URLs:</h3>
	<ul id="urls"></ul>
	<span>
		<button id="create">Create URL!</button>
	</span>

	<h3>Output:</h3>
	<span>
		<input type="url" value="" placeholder="https://r.leox.dev/bPkKCbY2ARG5" disabled id="out" />
		<button id="copy">Copy</button>
	</span>
</main>
<footer>
	<p>&copy; 2024-present <a href="https://leox.dev/">Leonard Lesinski</a>.</p>
</footer>
