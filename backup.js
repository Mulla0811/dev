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
        jslint: {
            js: {
                src: ["js/test.js"],
            },
            watch: {
                tasks: ["jslint"],
                files: ["js/test.js"],
            },
        },
        uglify: {
            my_target: {
                files: {
                    "dir2/app.min.js": ["js/test.js"],
                },
            },
        },

        http: {
            triggerPhp: {
                options: {
                    url: "http://your-server/path/to/your/script.php",
                    method: "GET",
                },
            },
        },
        myTask: {
            options: {
                // Define default values for your options
                greeting: "Hello",
                name: "World",
            },
        },
        prompt: {
            confirm: {
                options: {
                    questions: [
                        {
                            config: "confirmTask.confirmation",
                            type: "confirm",
                            message: "Do you want to proceed?",
                            default: false,
                        },
                    ],
                },
            },
            confirm_insert: {
                options: {
                    questions: [
                        {
                            config: "confirm_insert.confirmation",
                            type: "confirm",
                            message: "Do you want to insert update data?",
                            default: false,
                        },
                    ],
                },
            },
        },
        readOutput: {
            read: {
                src: "output.txt",
                dest: "globalVariable",
            },
        },
        shell: {
            phpScript: {
                command: 'echo <%= grunt.option("message") %>',
                options: {
                    callback: function (err, stdout, stderr, cb) {
                        // grunt.file.write('output.txt', stdout);
                        grunt.config.set("globalVariable", grunt.file.read("output.txt"));
                        // Handle the output or errors here
                        // console.log(stdout);
                        cb();
                    },
                    execOptions: {
                        // Additional options if needed
                    },
                },
            },
        },
    });

    // Load the plugin that provides the "uglify" task.

    grunt.loadNpmTasks("grunt-contrib-copy");
    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks("grunt-jslint");
    grunt.loadNpmTasks("grunt-contrib-uglify"); // Load the uglify plugin
    grunt.loadNpmTasks("grunt-shell");
    grunt.loadNpmTasks("grunt-http");
    grunt.loadNpmTasks("grunt-confirm");
    grunt.loadNpmTasks("grunt-prompt");

    grunt.registerTask("readOutput", function () {
        console.log(grunt.config("globalVariable"));
    });

    grunt.registerTask("getArgumet", "A custom task that takes arguments", function () {
        const server = grunt.option("server");

        if (server != "") {
            grunt.config.set("server", server);
        } else {
            grunt.config.set("server", server);
        }
        // Do something with the arguments
        // console.log('Argument 1:', server);

        // Your task logic goes here...
    });

    grunt.registerTask("conformTask", "My conform task", function () {
        // Retrieve confirmation value from prompt
        const confirmation = grunt.config("confirmTask.confirmation");
        if (confirmation) {
            grunt.log.writeln("Proceeding to the next task1.");

            grunt.config.set("shell.phpScript.data.message", "php /var/www/html/grunt_js/text.php");

            console.log(grunt.option);
            grunt.task.run("shell");
        } else {
            grunt.log.writeln("Aborted. Exiting.");
        }
    });

    grunt.registerTask("insert_update_task", "My table compair task", function () {
        // Retrieve confirmation value from prompt

        const confirm_insert = grunt.config("confirm_insert");

        if (confirm_insert.confirmation) {
            grunt.log.writeln("Proceeding to update isert task.");
            // grunt.task.run("shell");
        } else {
            grunt.log.writeln("Aborted. Exiting.");
        }
    });

    grunt.registerTask("myTask", "My custom task", function () {
        // Access the options using this.options
        console.log(this.options());
        const greeting = this.options().greeting;
        const name = this.options().name;

        // Log the values
        grunt.log.writeln(greeting + ", " + name + "!");
    });
    grunt.registerTask("default", ["getArgumet", "prompt:confirm", "conformTask", "readOutput", "prompt:confirm_insert", "insert_update_task"]);
    // Default task(s)
    // grunt.registerTask('default', ['getArgumet']);
    // grunt.registerTask('default', ['uglify']); // Define the default task(s).
    // grunt.registerTask('default',['copy:main','jslint:all']);
    grunt.registerTask("jshint", ["jslint", "watch"]);
    grunt.registerTask("watch", ["watch"]);
    grunt.registerTask("run", function () {
        console.log("run");
    });
};
