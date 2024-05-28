import { Buffer } from 'buffer';

function base64urlDecode(str: string) {
  return Buffer.from(str, 'base64url').toString('utf8');
};

function deobf(input: Buffer) {
  let prev = 0;
  const out = Buffer.alloc(input.byteLength);

  for (let i = 0; i < input.byteLength; i++) {
    const val = input.readUint8(i) + prev;
    prev = val % 256;
    out.writeUint8(prev, i);
  }

  return out;
}

function l(url: string) {
  const https = !url.startsWith('.');
  const www = url.endsWith('.');
  let realUrl = https ? url : url.slice(1);
  realUrl = www ? realUrl.slice(0, -1) : realUrl;
  return (https ? 'https://' : 'http://') + (www ? 'www.' : '') + deobf(Buffer.from(base64urlDecode(realUrl), 'utf8')).toString('utf8');
}

export async function GET({ params, setHeaders }) {
  const urls = params.url.split('/');
  const random = urls.length > 1;
  let loc = 'https://leox.dev/';
  if (random) loc = l(urls[Math.floor(Math.random() * urls.length)]); else loc = l(urls[0]);
  console.log(loc);
  setHeaders({
    'referrer-policy': 'unsafe-url',
    location: loc,
  });
	return new Response(null, { status: random ? 307 : 301 });
}