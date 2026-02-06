/* Minimal Prism.js Implementation */
/* Simple syntax highlighter for demo purposes */

(function () {
  "use strict";

  // Simple tokenizer for PHP, JavaScript, and HTML
  const languages = {
    php: {
      comment: /\/\*[\s\S]*?\*\/|\/\/.*/g,
      string: /(["'])(?:\\.|(?!\1)[^\\\r\n])*\1/g,
      keyword:
        /\b(class|function|public|private|protected|static|return|if|else|foreach|while|for|new|use|namespace|declare|strict_types|extends|implements|interface|trait|const|var|echo|require|include)\b/g,
      variable: /\$\w+/g,
      function: /\b\w+(?=\()/g,
      operator: /[+\-*\/%=<>!&|]+/g,
      punctuation: /[{}[\];(),.:]/g,
    },
    javascript: {
      comment: /\/\*[\s\S]*?\*\/|\/\/.*/g,
      string: /(["'`])(?:\\.|(?!\1)[^\\\r\n])*\1/g,
      keyword:
        /\b(const|let|var|function|class|if|else|for|while|return|import|export|default|async|await|try|catch|throw|new)\b/g,
      function: /\b\w+(?=\()/g,
      operator: /[+\-*\/%=<>!&|]+|=>|\.{3}/g,
      punctuation: /[{}[\];(),.:]/g,
    },
    html: {
      comment: /<!--[\s\S]*?-->/g,
      tag: /<\/?[\w-]+/g,
      attr: /\b[\w-]+(?==)/g,
      string: /(["'])(?:\\.|(?!\1)[^\\\r\n])*\1/g,
      punctuation: /[<>\/=]/g,
    },
  };

  function highlightCode(code, lang) {
    if (!languages[lang]) return code;

    const tokens = [];
    const rules = languages[lang];
    let remaining = code;

    // Extract tokens
    Object.keys(rules).forEach((type) => {
      remaining = remaining.replace(rules[type], (match) => {
        const placeholder = `__TOKEN_${tokens.length}__`;
        tokens.push({ type, value: match });
        return placeholder;
      });
    });

    // Replace placeholders with spans
    let highlighted = remaining;
    tokens.forEach((token, i) => {
      const span = `<span class="token ${token.type}">${escapeHtml(token.value)}</span>`;
      highlighted = highlighted.replace(`__TOKEN_${i}__`, span);
    });

    return highlighted;
  }

  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  // Highlight all code blocks
  function highlightAll() {
    document
      .querySelectorAll('pre code[class*="language-"]')
      .forEach((block) => {
        const lang = block.className.match(/language-(\w+)/)?.[1];
        if (lang) {
          const code = block.textContent;
          block.innerHTML = highlightCode(code, lang);
        }
      });
  }

  // Copy code functionality
  function setupCopyButtons() {
    document.querySelectorAll(".code-block-wrapper").forEach((wrapper) => {
      const btn = wrapper.querySelector(".copy-code-btn");
      const code = wrapper.querySelector("code");

      if (btn && code) {
        btn.addEventListener("click", async () => {
          try {
            await navigator.clipboard.writeText(code.textContent);
            btn.textContent = "✓ Copied!";
            btn.classList.add("copied");

            setTimeout(() => {
              btn.textContent = "Copy";
              btn.classList.remove("copied");
            }, 2000);
          } catch (err) {
            console.error("Failed to copy:", err);
          }
        });
      }
    });
  }

  // Initialize
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      highlightAll();
      setupCopyButtons();
    });
  } else {
    highlightAll();
    setupCopyButtons();
  }

  // Re-highlight on SPA page load
  window.addEventListener("pageLoaded", () => {
    setTimeout(() => {
      highlightAll();
      setupCopyButtons();
    }, 100);
  });

  // Export for manual use
  window.Prism = {
    highlightAll,
    highlightCode,
  };
})();
