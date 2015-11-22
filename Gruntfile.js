module.exports = function(grunt){
    
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify:{
            options: {
                banner: '/**\n\n\t5-designs - company\n\t<%= pkg.name %>\n\tcreated with love in Austria\n\n\tVersion: <%= pkg.version %>\n\tLast changed: <%= grunt.template.today("dd-mm-yyyy") %>\n\tWebsite: http://www.acolyte.5-designs.com\n\tGithub: https://github.com/5-designs/acolyte\n\n*/\n\n'
            },
            dist:{
                src:['assets/js/*.js','assets/js/**/*.js'],
                dest:'acolyte/acolyte.min.js'
            }
        },
        sass: {
            dist: {
                files: {
                    'styles/app.css': 'assets/styles/app.scss',
                    'acolyte/acolyte.css': 'assets/styles/acolyte.scss'
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