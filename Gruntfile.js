module.exports = function(grunt){
    
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify:{
            options: {
                banner: '/**\n\n\t' +
                        '5-designs - company\n\t' +
                        '<%= pkg.name %>\n\t' +
                        'created with love in Austria\n\n\t' +
                        'Version: <%= pkg.version %>\n\t' +
                        'Last changed: <%= grunt.template.today("dd-mm-yyyy") %>\n\t' +
                        'Website: http://www.acolyte.5-designs.com\n\t' +
                        'Github: https://github.com/5-designs/acolyte\n\n*/\n\n'
            },
            dist:{
                src:['assets/js/*.js','assets/js/**/*.js'],
                dest:'acolyte/acolyte.min.js'
            }
        },
        sass: {
            dist: {
                files: {
                    'acolyte/acolyte.css': 'assets/css/acolyte.scss'
                }
            }
        },
        watch:{
            js:{
                files: ['assets/js/*.js','assets/js/**/*.js'],
                tasks: ['uglify']
            },
            styles:{
                files: ['assets/css/*.scss', 'assets/css/*/*.scss'],
                tasks: ['sass']
            }
        }
    });
    
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    
    grunt.registerTask('default',['uglify','sass']);
};