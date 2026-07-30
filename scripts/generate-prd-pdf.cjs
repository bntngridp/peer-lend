#!/usr/bin/env node
/**
 * LendFlow PRD — Markdown to HTML + PDF Generator
 * Uses Node.js built-in modules only (no external dependencies)
 * PDF is generated via Chrome headless
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const INPUT_MD   = path.join(__dirname, '../docs/PRD.md');
const OUTPUT_HTML = path.join(__dirname, '../docs/PRD.html');
const OUTPUT_PDF  = path.join(__dirname, '../docs/PRD.pdf');

// ─── Simple Markdown Parser ────────────────────────────────────────────────────
function parseMd(md) {
  let html = '';
  const lines = md.split('\n');
  let i = 0;
  let inCode = false;
  let codeLang = '';
  let codeBuffer = [];
  let inTable = false;
  let tableBuffer = [];
  let inBlockquote = false;
  let blockquoteBuffer = [];

  const escHtml = (str) =>
    str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

  const inlineFormat = (text) => {
    // Bold **text**
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    // Italic *text*
    text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
    // Inline code `code`
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    // Links [text](url)
    text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');
    return text;
  };

  const flushTable = () => {
    if (!tableBuffer.length) return '';
    let out = '<table>\n';
    tableBuffer.forEach((row, idx) => {
      const cells = row.split('|').filter((c, i, arr) => i > 0 && i < arr.length - 1);
      if (idx === 0) {
        out += '<thead><tr>' + cells.map(c => `<th>${inlineFormat(c.trim())}</th>`).join('') + '</tr></thead>\n<tbody>\n';
      } else if (idx === 1 && /^[\s|:-]+$/.test(row)) {
        // separator row — skip
      } else {
        out += '<tr>' + cells.map(c => `<td>${inlineFormat(c.trim())}</td>`).join('') + '</tr>\n';
      }
    });
    out += '</tbody>\n</table>\n';
    tableBuffer = [];
    inTable = false;
    return out;
  };

  while (i < lines.length) {
    const line = lines[i];

    // Code blocks
    if (line.startsWith('```')) {
      if (!inCode) {
        inCode = true;
        codeLang = line.slice(3).trim();
        codeBuffer = [];
        i++;
        continue;
      } else {
        const lang = codeLang ? ` class="language-${codeLang}"` : '';
        html += `<pre><code${lang}>${escHtml(codeBuffer.join('\n'))}</code></pre>\n`;
        inCode = false;
        codeLang = '';
        codeBuffer = [];
        i++;
        continue;
      }
    }
    if (inCode) {
      codeBuffer.push(line);
      i++;
      continue;
    }

    // Tables
    if (line.startsWith('|')) {
      if (!inTable) inTable = true;
      tableBuffer.push(line);
      i++;
      // Peek if next line is still table
      if (i >= lines.length || !lines[i].startsWith('|')) {
        html += flushTable();
      }
      continue;
    }
    if (inTable) {
      html += flushTable();
    }

    // Blockquote
    if (line.startsWith('> ')) {
      html += `<blockquote><p>${inlineFormat(line.slice(2))}</p></blockquote>\n`;
      i++;
      continue;
    }

    // HR
    if (/^---+$/.test(line.trim())) {
      html += '<hr>\n';
      i++;
      continue;
    }

    // Headings
    const headMatch = line.match(/^(#{1,6})\s+(.+)$/);
    if (headMatch) {
      const level = headMatch[1].length;
      const text = inlineFormat(headMatch[2]);
      // Generate anchor id
      const id = headMatch[2].toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-');
      html += `<h${level} id="${id}">${text}</h${level}>\n`;
      i++;
      continue;
    }

    // Unordered list
    if (/^[\s]*[-*+]\s/.test(line)) {
      const indent = line.match(/^(\s*)/)[1].length;
      const content = inlineFormat(line.replace(/^[\s]*[-*+]\s/, ''));
      if (indent === 0) {
        html += `<ul><li>${content}</li></ul>\n`;
      } else {
        html += `<ul style="margin-left:${indent * 8}px"><li>${content}</li></ul>\n`;
      }
      i++;
      continue;
    }

    // Ordered list
    if (/^\d+\.\s/.test(line)) {
      const content = inlineFormat(line.replace(/^\d+\.\s/, ''));
      html += `<ol><li>${content}</li></ol>\n`;
      i++;
      continue;
    }

    // Empty line
    if (line.trim() === '') {
      html += '\n';
      i++;
      continue;
    }

    // Paragraph
    html += `<p>${inlineFormat(line)}</p>\n`;
    i++;
  }

  return html;
}

// ─── Read markdown ─────────────────────────────────────────────────────────────
console.log('📖 Reading PRD.md...');
const mdContent = fs.readFileSync(INPUT_MD, 'utf8');
const bodyHtml = parseMd(mdContent);

// ─── Build full HTML ──────────────────────────────────────────────────────────
const fullHtml = `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRD — LendFlow P2P Lending & Collateral FinTech Platform</title>
  <meta name="description" content="Product Requirements Document untuk LendFlow — Platform P2P Lending FinTech berbasis Laravel 11, PostgreSQL 16, dan Redis.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    
    :root {
      --primary: #0f3460;
      --primary-light: #1a5276;
      --accent: #3182ce;
      --accent-light: #ebf8ff;
      --success: #276749;
      --warning: #7b5e00;
      --text-primary: #1a202c;
      --text-secondary: #4a5568;
      --text-muted: #718096;
      --bg-primary: #ffffff;
      --bg-secondary: #f7fafc;
      --bg-tertiary: #edf2f7;
      --border: #e2e8f0;
      --border-dark: #cbd5e0;
    }

    html { scroll-behavior: smooth; }
    
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      font-size: 14px;
      line-height: 1.75;
      color: var(--text-primary);
      background: var(--bg-primary);
      -webkit-font-smoothing: antialiased;
    }

    /* ─── Cover Page ─── */
    .cover {
      background: linear-gradient(135deg, #0d1b2a 0%, #1a2f4e 40%, #0f3460 70%, #1565c0 100%);
      color: #fff;
      padding: 100px 80px 80px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .cover::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 70%, rgba(49,130,206,0.15) 0%, transparent 50%),
                  radial-gradient(circle at 70% 30%, rgba(99,179,237,0.1) 0%, transparent 50%);
    }
    .cover > * { position: relative; z-index: 1; }
    .cover .badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.25);
      padding: 8px 24px;
      border-radius: 100px;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      margin-bottom: 32px;
      color: rgba(255,255,255,0.9);
    }
    .cover h1 {
      font-size: 56px;
      font-weight: 800;
      margin-bottom: 8px;
      background: linear-gradient(135deg, #ffffff, #90cdf4);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: -1px;
    }
    .cover .tagline {
      font-size: 20px;
      opacity: 0.75;
      margin-bottom: 12px;
      font-weight: 300;
    }
    .cover .subtitle {
      font-size: 15px;
      opacity: 0.6;
      margin-bottom: 56px;
    }
    .meta-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      max-width: 640px;
      margin: 0 auto;
    }
    .meta-item {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 16px;
      padding: 18px 16px;
      backdrop-filter: blur(10px);
    }
    .meta-item .label {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      opacity: 0.55;
      margin-bottom: 6px;
      font-weight: 500;
    }
    .meta-item .value {
      font-size: 15px;
      font-weight: 700;
      opacity: 0.95;
    }

    /* ─── Main Content ─── */
    .page-content {
      max-width: 900px;
      margin: 0 auto;
      padding: 64px 80px 100px;
    }

    /* ─── Headings ─── */
    h1 {
      font-size: 30px;
      font-weight: 800;
      color: var(--primary);
      margin: 64px 0 20px;
      padding-bottom: 12px;
      border-bottom: 3px solid var(--primary);
      letter-spacing: -0.5px;
    }
    h1:first-child { margin-top: 0; }
    h2 {
      font-size: 20px;
      font-weight: 700;
      color: var(--primary-light);
      margin: 48px 0 16px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    h2::before {
      content: '';
      display: inline-block;
      width: 5px;
      height: 22px;
      background: linear-gradient(135deg, var(--accent), var(--primary));
      border-radius: 3px;
      flex-shrink: 0;
    }
    h3 {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-primary);
      margin: 32px 0 12px;
    }
    h4 {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-secondary);
      margin: 24px 0 10px;
    }
    
    /* ─── Paragraphs & Text ─── */
    p { margin-bottom: 16px; color: var(--text-secondary); }
    strong { font-weight: 700; color: var(--text-primary); }
    em { font-style: italic; color: var(--text-secondary); }
    a { color: var(--accent); text-decoration: none; }
    a:hover { text-decoration: underline; }

    /* ─── Lists ─── */
    ul, ol { padding-left: 28px; margin-bottom: 16px; }
    ul { list-style: none; }
    ul li::before { content: '▸'; color: var(--accent); margin-left: -20px; padding-right: 8px; font-size: 10px; }
    li { margin-bottom: 8px; color: var(--text-secondary); }

    /* ─── Code ─── */
    code {
      font-family: 'Fira Code', 'JetBrains Mono', 'Courier New', monospace;
      background: var(--bg-tertiary);
      border: 1px solid var(--border);
      padding: 2px 8px;
      border-radius: 5px;
      font-size: 12px;
      color: #c0392b;
      font-weight: 500;
    }
    pre {
      background: #1a202c;
      border-radius: 14px;
      padding: 28px 32px;
      overflow-x: auto;
      margin: 24px 0;
      border-left: 5px solid var(--accent);
      box-shadow: 0 4px 24px rgba(0,0,0,0.15);
    }
    pre code {
      background: none;
      border: none;
      padding: 0;
      color: #e2e8f0;
      font-size: 12.5px;
      line-height: 1.7;
      font-weight: 400;
    }

    /* ─── Tables ─── */
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 24px 0;
      font-size: 13px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 1px 8px rgba(0,0,0,0.06);
    }
    thead tr {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
    }
    th {
      padding: 14px 18px;
      text-align: left;
      font-weight: 600;
      font-size: 12px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    tbody tr { transition: background 0.15s; }
    tbody tr:nth-child(even) { background: var(--bg-secondary); }
    tbody tr:hover { background: var(--accent-light); }
    td {
      padding: 12px 18px;
      border-bottom: 1px solid var(--border);
      vertical-align: top;
      color: var(--text-secondary);
    }
    td:first-child { font-weight: 500; color: var(--text-primary); }

    /* ─── Blockquote ─── */
    blockquote {
      background: var(--accent-light);
      border-left: 5px solid var(--accent);
      border-radius: 0 12px 12px 0;
      padding: 20px 24px;
      margin: 24px 0;
    }
    blockquote p {
      margin: 0;
      color: #2a4a7f;
      font-weight: 500;
    }

    /* ─── HR ─── */
    hr {
      border: none;
      border-top: 2px solid var(--border);
      margin: 48px 0;
    }

    /* ─── Footer ─── */
    .footer {
      background: var(--primary);
      color: rgba(255,255,255,0.7);
      text-align: center;
      padding: 32px;
      font-size: 12px;
    }
    .footer strong { color: white; }

    /* ─── Print / PDF ─── */
    @media print {
      body { font-size: 11px; }
      .page-content { padding: 32px 40px 60px; max-width: 100%; }
      .cover { padding: 60px 40px; }
      .cover h1 { font-size: 40px; }
      h1 { page-break-before: always; font-size: 24px; }
      h2 { font-size: 17px; }
      pre, table, blockquote { page-break-inside: avoid; }
      .footer { page-break-inside: avoid; }
      a { color: inherit; }
    }
  </style>
</head>
<body>

<!-- ─── Cover Page ─── -->
<div class="cover">
  <div class="badge">Product Requirements Document</div>
  <h1>LendFlow</h1>
  <div class="tagline">Platform P2P Lending &amp; Collateral FinTech</div>
  <div class="subtitle">PHP 8.3 &middot; Laravel 11 &middot; PostgreSQL 16 &middot; Redis &middot; Docker</div>
  <div class="meta-grid">
    <div class="meta-item">
      <div class="label">Versi</div>
      <div class="value">v1.0.0</div>
    </div>
    <div class="meta-item">
      <div class="label">Tanggal</div>
      <div class="value">30 Juli 2026</div>
    </div>
    <div class="meta-item">
      <div class="label">Status</div>
      <div class="value">Active Dev</div>
    </div>
    <div class="meta-item">
      <div class="label">Penulis</div>
      <div class="value">Bintang R.P.</div>
    </div>
    <div class="meta-item">
      <div class="label">Backend</div>
      <div class="value">Laravel 11</div>
    </div>
    <div class="meta-item">
      <div class="label">Database</div>
      <div class="value">PostgreSQL 16</div>
    </div>
  </div>
</div>

<!-- ─── Main Content ─── -->
<div class="page-content">
${bodyHtml}
</div>

<!-- ─── Footer ─── -->
<div class="footer">
  <p><strong>LendFlow PRD v1.0.0</strong> &mdash; Product Requirements Document &mdash; Dibuat 30 Juli 2026</p>
  <p style="margin-top:6px">Repository: <strong>bntngridp/peer-lend</strong> &middot; Stack: PHP 8.3 &middot; Laravel 11 &middot; PostgreSQL 16 &middot; Redis &middot; Docker</p>
</div>

</body>
</html>`;

// ─── Write HTML ─────────────────────────────────────────────────────────────
fs.writeFileSync(OUTPUT_HTML, fullHtml, 'utf8');
console.log('✅ HTML generated:', OUTPUT_HTML);

// ─── Generate PDF via Chrome Headless ─────────────────────────────────────
const chromePath = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const chromeCmd = `"${chromePath}" --headless=new --disable-gpu --no-sandbox --print-to-pdf="${OUTPUT_PDF}" --print-to-pdf-no-header --no-pdf-header-footer "${OUTPUT_HTML}" 2>/dev/null`;

try {
  console.log('🖨️  Generating PDF via Chrome headless...');
  execSync(chromeCmd, { timeout: 30000 });
  const stat = fs.statSync(OUTPUT_PDF);
  console.log(`✅ PDF generated: ${OUTPUT_PDF} (${(stat.size / 1024).toFixed(1)} KB)`);
} catch (e) {
  console.error('⚠️  PDF generation failed:', e.message);
  console.log('💡 HTML is available at:', OUTPUT_HTML);
}

console.log('\n✨ Done! Files created:');
console.log('   📄 docs/PRD.md   (Markdown — for Agents)');
console.log('   🌐 docs/PRD.html (HTML — for Browser)');
console.log('   📕 docs/PRD.pdf  (PDF — for Distribution)');
