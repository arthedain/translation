Nova.booting((Vue, router, store) => {
	Vue.filter('striphtml', function (value) {
	  return value.replace(/(<([^>]+)>)/ig,"");
	});
  router.addRoutes([
    {
      name: 'translation',
      path: '/translation',
      component: require('./components/Tool'),
    },
  ])
})
