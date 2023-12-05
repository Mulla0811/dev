module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        // uglify: {
        //   options: {
        //     banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
        //   },
        //   build: {
        //     src: 'src/<%= pkg.name %>.js',
        //     dest: 'build/<%= pkg.name %>.min.js'
        //   },
        copy: {
            main: {
                src: "dir1/**",
                dest: "dir2/",
            },
        },
        
        
    });

    // Load the plugin that provides the "uglify" task.
///tests sb
    grunt.loadNpmTasks("grunt-contrib-copy");
    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks("grunt-jslint");
    grunt.loadNpmTasks("grunt-contrib-uglify"); // Load the uglify plugin
    grunt.loadNpmTasks("grunt-shell");
    grunt.loadNpmTasks("grunt-http");
    grunt.loadNpmTasks("grunt-confirm");
    grunt.loadNpmTasks("grunt-prompt");

    // Default task(s)
    // grunt.registerTask('default', ['getArgumet']);
    // grunt.registerTask('default', ['uglify']); // Define the default task(s).
    // grunt.registerTask('default',['copy:main','jslint:all']);

    
    // grunt.registerTask("default", ["getArgumet", "prompt:confirm", "conformTask",  "prompt:confirm_insert", "insert_update_task"]);
    // grunt.registerTask("jshint", ["jslint", "watch"]);
    // grunt.registerTask("watch", ["watch"]);
    // grunt.registerTask("run", function () {
    //     console.log("run");
    // });
};
