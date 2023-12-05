var output_data = '';
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
            confirm_create_table: {
                options: {
                    questions: [
                        {
                            config: "confirm_create_table.confirmation",
                            type: "confirm",
                            message: "Do you want to create tables?",
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
                command: '<%= grunt.config("shell.dynamicCommand.options.data.command_val") %>',
                options: {
                    callback: function (err, stdout, stderr, cb) {
                        const compair_data = JSON.parse(stdout);
                        if (compair_data["process"] == "compair") {
                            output_data += "after compair two table of database get below data \n\n"+stdout+"\n";
                            var file_name = grunt.config("compair_table_name");
                            writeInfile(file_name,output_data);
                            if (!compair_data["compair_data_found"]) {
                                grunt.log.writeln("");
                                grunt.fail.fatal(compair_data["message"]);
                            } else {
                                console.log(compair_data["result_data"]);
                                grunt.config.set("compair_data", compair_data["result_data"]);
                                grunt.log.writeln("\n\n" + compair_data["message"]);
                            }
                        } else if (compair_data["process"] == "insert_update") {
                            output_data += "after insert update data of database get below data \n\n"+stdout+"\n";
                            var file_name = grunt.config("compair_table_name");
                            writeInfile(file_name,output_data);
                            // grunt.fail.fatal("fails");
                        }else if (compair_data["process"] == "compair_hole_db") {
                            
                            output_data += "\n after compair two db get below data \n\n"+stdout+"\n";
                            var file_name = grunt.config("compair_db_file_name");
                            
                            writeInfile(file_name,output_data);
                           
                            if (!compair_data["compair_data_found"]) {
                                grunt.log.writeln("");
                                grunt.fail.fatal(compair_data["message"]);
                            } else {
                                // console.log(compair_data["result_data"]);
                                grunt.config.set("compair_hole_db_data", compair_data["result_data"]);
                                grunt.log.writeln("\n\n" + compair_data["message"]);
                            }
                            
                        } else if (compair_data["process"] == "create_db") {
                            output_data += "\n after create database get below data \n\n"+stdout+"\n";
                            var file_name = grunt.config("compair_db_file_name");
                            writeInfile(file_name,output_data);
                            // console.log(compair_data);
                            // grunt.fail.fatal("fails");
                        }

                        cb();
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
    grunt.loadNpmTasks('grunt-contrib-watch');
    var fs = require('fs');


    // Default task(s)
    // grunt.registerTask('default', ['getArgumet']);
    // grunt.registerTask('default', ['uglify']); // Define the default task(s).
    // grunt.registerTask('default',['copy:main']);

/* compair single table value*/
    grunt.registerTask("getArgumet", "A custom task that takes arguments", function () {
        var timestamp = new Date().toISOString().replace(/[-:.]/g,"");  
        var file_name = "compair_db_"+  timestamp+".txt";
        var filePath = "out_put_file/"+file_name;
        output_data = 'Content of compair table of two database.\n\n';
        writeInfile(filePath,output_data);
        grunt.config.set("compair_table_name", filePath);

        const server = grunt.option("server");
        const command = grunt.option("command");
        if (server != "" && server != undefined && server != null) {
            grunt.log.writeln("server data get successfully.");
            grunt.config.set("server", server);
        } else {
            grunt.fail.fatal("Task stopped because of a server data not found.");
        }

        if (command != "" && command != undefined && command != null) {
            grunt.log.writeln("command get successfully.");
            grunt.config.set("command", command);
        } else {
            grunt.fail.fatal("Task stopped because of a command not found.");
        }
    });

    // compair db process
    grunt.registerTask("conformTask", "My conform task", function () {
        const confirmation = grunt.config("confirmTask.confirmation");
        if (confirmation) {
            grunt.log.writeln("Proceeding to comapir database.");
            const command_val = grunt.option("command") + " server=" + grunt.config("server");
            // Set the dynamic command template data
            grunt.config.set("shell.dynamicCommand.options.data", {
                command_val: command_val,
            });
            grunt.task.run("shell:phpScript");
        } else {
            grunt.log.writeln("Aborted. Exiting.");
        }
    });

    // insert update db process
    grunt.registerTask("insert_update_task", "My table compair task", function () {
        
        const confirm_insert = grunt.config("confirm_insert");
        if (confirm_insert.confirmation) {
            grunt.log.writeln("Proceeding to update insert task.");
            
            const command_val = grunt.option("insert_commnd") + " data='" + grunt.config("compair_data") + "'";
        
            // Set the dynamic command template data
            grunt.config.set("shell.dynamicCommand.options.data", {
                command_val: command_val,
            });
            grunt.task.run("shell:phpScript");
        } else {
            grunt.fail.fatal("Aborted. Exiting.");
        }
    });

/* compair single table value*/

/* compair hole db value*/

    grunt.registerTask("getArgumetCD", "A custom task that takes arguments", function () {
        var timestamp = new Date().toISOString().replace(/[-:.]/g,"");  
        var file_name = "create_db_"+  timestamp+".txt";
        var filePath = "out_put_file/"+file_name;
        output_data = 'Content of compair two database.\n\n';
        writeInfile(filePath,output_data);
        grunt.config.set("compair_db_file_name", filePath);
        const server = grunt.option("server");
        const command = grunt.option("command");
        if (server != "" && server != undefined && server != null) {
            grunt.log.writeln("server data get successfully.");
            grunt.config.set("server", server);
        } else {
            grunt.fail.fatal("Task stopped because of a server data not found.");
        }

        if (command != "" && command != undefined && command != null) {
            grunt.log.writeln("command get successfully.");
            grunt.config.set("command", command);
        } else {
            grunt.fail.fatal("Task stopped because of a command not found.");
        }
    });

    grunt.registerTask("conformTaskCD", "My conform task", function () {
        const confirmation = grunt.config("confirmTask.confirmation");
        if (confirmation) {
            grunt.log.writeln("Proceeding to comapir database.");
            const command_val = grunt.option("command") + " server=" + grunt.config("server");
            console.log(command_val)
            // Set the dynamic command template data
            grunt.config.set("shell.dynamicCommand.options.data", {
                command_val: command_val,
            });
            grunt.task.run("shell:phpScript");
        } else {
            grunt.log.writeln("Aborted. Exiting.");
        }
    });
    grunt.registerTask("create_table", "My table compair task", function () {
        
        const confirm_create_table = grunt.config("confirm_create_table");
        console.log(confirm_create_table)
        if (confirm_create_table.confirmation) {
            grunt.log.writeln("Proceeding to create table task.");
            
            const command_val = grunt.option("create_table_commnd") + " data='" + grunt.config("compair_hole_db_data") + "'";
            console.log(command_val);

            // Set the dynamic command template data

            grunt.config.set("shell.dynamicCommand.options.data", {
                command_val: command_val,
            });
            grunt.task.run("shell:phpScript");
        } else {
            grunt.log.writeln("Aborted. Exiting.");
        }
    });

/* compair hole db value*/
    grunt.registerTask("compair_table", ["getArgumet", "prompt:confirm", "conformTask", "prompt:confirm_insert", "insert_update_task"]);

    grunt.registerTask("default", ["getArgumetCD", "prompt:confirm", "conformTaskCD","prompt:confirm_create_table", "create_table"]);
    
    // grunt.registerTask("jshint", ["jslint", "watch"]);
    // grunt.registerTask("watch", ["watch"]);
    // grunt.registerTask("run", function () {
    //     console.log("run");
    // });

    function writeInfile(filePath,fileContent){
        // grunt.file.write(filePath, fileContent.join('\n'));
        fs.writeFileSync(filePath, fileContent);
    }
};
    