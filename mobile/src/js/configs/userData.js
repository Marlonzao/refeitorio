app.run(function($rootScope) {
    $rootScope.userData = {
        name: '',
        email: '',
        availableModules: '',
        bio: '',
        photo: ''
    }

    $rootScope.vars = {
        mealPrice: 7.75
    }

    $rootScope.enviroments = '';

    $rootScope.selectedEnviroment = {
        name: 'Campus Teste',
        type: 'school',
        id: 1
    }
})