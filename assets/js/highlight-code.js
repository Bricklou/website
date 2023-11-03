let timeoutMemory = null;

export function highlightCode() {
    const codeBlocks = document.querySelectorAll('pre code[class*="language-"]');
    
    codeBlocks.forEach((codeBlock) => {
        const parent = codeBlock.parentElement.parentElement;

        parent.classList.add('mb-2', 'block', 'min-h-full', 'overflow-auto');

        addLanguageLabel(codeBlock, parent);
    })
}

function addLanguageLabel(codeBlock, preEl) {
  preEl.classList.add('relative');

  // get "data-lang" attribute from element
  const name = codeBlock.getAttribute('data-lang');
  const languageLabel = document.createElement('small');
  languageLabel.classList.add(
    'absolute',
    'top-0',
    'right-0',
    'py-1',
    'px-4',
    'font-mono',
    'text-gray-200',
    'bg-slate-950',
    'rounded-bl-md',
    'select-none',
    'cursor-pointer',
  );
  languageLabel.textContent = name;
  languageLabel.title = 'Copier le code';

  languageLabel.addEventListener('click', () => {
    navigator.clipboard.writeText(codeBlock.textContent);

    languageLabel.classList.remove('bg-slate-950');
    languageLabel.classList.add('bg-green-600');
    languageLabel.textContent = 'Copié !';

    clearTimeout(timeoutMemory);

    timeoutMemory = setTimeout(() => {
      languageLabel.textContent = name;
      languageLabel.classList.remove('bg-green-600');
      languageLabel.classList.add('bg-slate-950');
    }, 2000);
  });

  preEl.append(languageLabel);
}