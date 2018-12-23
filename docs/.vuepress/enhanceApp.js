export default ({
    Vue, // the version of Vue being used in the VuePress app
    options, // the options for the root Vue instance
    router, // the router instance for the app
    siteData // site metadata
}) => {
    if (process.env.NODE_ENV === 'production' && typeof window !== 'undefined') {
        const FATHOM_ID = 'YTULG';
        const FATHOM_URL = '//fathom.wrkcpt.co/tracker.js';

        (function(f, a, t, h, o, m){
            a[h]=a[h]||function(){
                (a[h].q=a[h].q||[]).push(arguments)
            }
            o=f.createElement('script'), m=f.getElementsByTagName('script')[0]
            o.async=1
            o.src=t
            o.id='fathom-script'
            m.parentNode.insertBefore(o,m)
        })(document, window, '//fathom.wrkcpt.co/tracker.js', 'fathom')

        fathom('set', 'siteId', FATHOM_ID)

        router.afterEach(function(to) {
            fathom('set', 'trackerUrl', to.fullPath)
            fathom('trackPageview')
        })
    }
}
