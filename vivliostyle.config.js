// @ts-check
/** @type {import('@vivliostyle/cli').VivliostyleConfigSchema} */
const vivliostyleConfig = {
  title: '緯日辞典', // populated into 'publication.json', default to 'title' of the first entry or 'name' in 'package.json'.
  author: 'Zaslon', // default to 'author' in 'package.json' or undefined
  // language: 'la',
  // readingProgression: 'rtl', // reading progression direction, 'ltr' or 'rtl'.
  // size: 'A4',
  // theme: '', // .css or local dir or npm package. default to undefined
  image: 'ghcr.io/vivliostyle/cli:7.3.0',
  entry: [ // **required field**
    // 'introduction.md', // 'title' is automatically guessed from the file (frontmatter > first heading)
    // {
    //   path: 'epigraph.md',
    //   title: 'おわりに', // title can be overwritten (entry > file),
    //   theme: '@vivliostyle/theme-whatever' // theme can be set individually. default to root 'theme'
    // },
    // 'glossary.html' // html is also acceptable
    "e.html","a.html","o.html","i.html","u.html","h.html","k.html","s.html","t.html","c.html","n.html","r.html","m.html","p.html","f.html","g.html","z.html","d.html","b.html","v.html","1.html"
  ], // 'entry' can be 'string' or 'object' if there's only single markdown file
  entryContext: './manuscripts', // default to '.' (relative to 'vivliostyle.config.js')
  output: [ // path to generate draft file(s). default to '{title}.pdf'
  './緯日辞典.pdf', // the output format will be inferred from the name.
    // {
    //   path: './book',
    //   format: 'webpub',
    // },
  ],
  timeout: 1200000,
  // workspaceDir: '.vivliostyle', // directory which is saved intermediate files.
  // toc: true, // whether generate and include ToC HTML or not, default to 'false'.
  // cover: './cover.png', // cover image. default to undefined.
  // vfm: { // options of VFM processor
  //   replace: [ // specify replace handlers to modify HTML outputs
  //     {
  //       // This handler replaces {current_time} to a current local time tag.
  //       test: /{current_time}/,
  //       match: (_, h) => {
  //         const currentTime = new Date().toLocaleString();
  //         return h('time', { datetime: currentTime }, currentTime);
  //       },
  //     },
  //   ],
  //   hardLineBreaks: true, // converts line breaks of VFM to <br> tags. default to 'false'.
  //   disableFormatHtml: true, // disables HTML formatting. default to 'false'.
  // },
};

module.exports = vivliostyleConfig;
