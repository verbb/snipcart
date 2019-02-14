export default ({
    Vue, // the version of Vue being used in the VuePress app
    options, // the options for the root Vue instance
    router, // the router instance for the app
    siteData // site metadata
}) => {
    if (process.env.NODE_ENV !== 'development' && typeof window !== 'undefined') {
        // Fathom snippet already injected by Netflify; just register pageViews
        if (typeof fathom !== 'undefined') {
            router.afterEach(function(to) {
                fathom('set', 'trackerUrl', to.fullPath)
                fathom('trackPageview')
            });
        }
    }
}
