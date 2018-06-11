// app.config(function Config($httpProvider, jwtOptionsProvider) {
//     // TODO: Toda vez que receber um http status 401, redirecionar para a view Login
//     jwtOptionsProvider.config({
//         tokenGetter: function(options) {
//             if (options.url.substr(options.url.length - 5) == '.html') {
//                 return null;
//             }
            
//             return localStorage.getItem('id_token');
//         },
//         unauthenticatedRedirector: ['$state', function($state) {
//             $state.go('Login');
//         }],
//         whiteListedDomains: ['localhost']
//     });

//     $httpProvider.interceptors.push('jwtInterceptor');
// })

app.config(['$httpProvider', function ($httpProvider) {
    if (localStorage.getItem("id_token")) {
        $httpProvider.defaults.headers.common['Authorization'] = "Bearer "+localStorage.getItem("id_token");
    }
}])