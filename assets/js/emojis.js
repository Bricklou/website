import twemoji from '@twemoji/api';

export function parseEmojis() {
  const article = document.querySelector('article.post');

  if (!article) {
    return;
  }

  twemoji.parse(article, {
    base: '/assets/',
    folder: 'emojis',
    ext: '.svg',
    className: 'twemoji',
  });
}