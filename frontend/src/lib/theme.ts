const KEY = 'gaweqi_theme'

export function initTheme() {
  const saved = localStorage.getItem(KEY)
  if (saved === 'dark' || saved === 'light') {
    document.documentElement.setAttribute('data-theme', saved)
  }
}

export function toggleTheme() {
  const current =
    document.documentElement.getAttribute('data-theme') ??
    (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
  const next = current === 'dark' ? 'light' : 'dark'
  document.documentElement.setAttribute('data-theme', next)
  localStorage.setItem(KEY, next)
}
