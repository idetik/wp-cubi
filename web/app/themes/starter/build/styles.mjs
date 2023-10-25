import glob from 'glob';
import noderesolve from '@rollup/plugin-node-resolve';
import scss from 'rollup-plugin-scss';
import postcss from 'rollup-plugin-postcss';
import packageImporter from 'node-sass-package-importer';
import autoprefixer from 'autoprefixer';
import flexbugs from 'postcss-flexbugs-fixes';
import gap from 'postcss-gap-properties';
import path from 'path';
import fs from 'fs';
import cleanPattern from './helpers/clean.mjs';
import { loggerPlugin } from './helpers/logger.mjs';

const destination = './dist/styles';

const tasks = [];

const files = glob.sync('assets/styles/*.js');
files.forEach((file) => {
    const output = `${destination}/${path.basename(file, '.js')}.css`;
    tasks.push({
        input: file,
        output: {
            dir: destination
        },
        plugins: [
            loggerPlugin({
                message: `Build styles ${path.basename(output)} into ${destination}`,
                type: 'taskTitle'
            }),
            {
                name: 'styletik',
                buildStart() {
                    cleanPattern('dist/styles/*.css', true);
                },
                writeBundle(options) {
                    fs.unlinkSync(`${options.dir}/${path.basename(file)}`);
                }
            },
            noderesolve(),
            scss({
                output,
                importer: packageImporter(),
                outputStyle: 'compressed',
                watch: 'assets/styles'
            }),
            postcss({
                to: output,
                plugins: [
                    autoprefixer({
                        grid: true
                    }),
                    flexbugs(),
                    gap()
                ],
                sourceMap: process.env.NODE_ENV !== 'production' ? 'inline' : false,
                minimize: {
                    preset: 'default'
                }
            })
        ]
    });
});

export default tasks;
