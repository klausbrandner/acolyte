module.exports = function(grunt){
    
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify:{
            dist:{
                src:['assets/js/*.js','assets/js/**/*.js'],
                dest:'js/acolyte.min.js'
            }
        },
        sass: {
            dist: {
                files: {
                    'styles/app.css': 'assets/styles/app.scss'
                }
            }
        },
        watch:{
            js:{
                files: ['assets/js/*.js','assets/js/**/*.js'],
                tasks: ['uglify']
            },
            styles:{
                files: ['assets/styles/*.scss'],
                tasks: ['sass']
            }
        }
    });
    
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    
    grunt.registerTask('default',['uglify','sass']);
};