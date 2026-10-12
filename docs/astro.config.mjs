import { defineSilarhiDocs } from '@silarhi/docs-kit'

export default defineSilarhiDocs({
    slug: 'cfonb-parser',
    title: 'CFONB Parser',
    description: 'A PHP parser for CFONB 120 and 240 bank files, with zero dependencies.',
    sidebar: [
        { label: 'Getting started', items: [{ autogenerate: { directory: 'getting-started' } }] },
        { label: 'Guides', items: [{ autogenerate: { directory: 'guides' } }] },
        { label: 'Reference', items: [{ autogenerate: { directory: 'reference' } }] },
    ],
    starlight: {
        // The default branch is 6.x (one branch per major), not main
        editLink: { baseUrl: 'https://github.com/silarhi/cfonb-parser/edit/6.x/docs/' },
    },
})
