import commonjs from '@rollup/plugin-commonjs';
import noderesolve from '@rollup/plugin-node-resolve';
import { babel } from '@rollup/plugin-babel';
import { terser } from 'rollup-plugin-terser';
import path from 'path';
import cleanPattern from './helpers/clean.mjs';
import { loggerPlugin, logger } from './helpers/logger.mjs';

const tasks = [];

const scriptsPlugin = [
    loggerPlugin((options) => logger(`Build scripts ${path.basename(options.output[0].file || '')} into ${path.dirname(options.output[0].file || 'dist/scripts')}`, 'taskTitle')),
    {
        name: 'scriptetik',
        buildStart() {
            cleanPattern('dist/scripts/*.js', true);
        }
    },
    commonjs(),
    noderesolve(),
    babel({
        babelHelpers: 'bundled',
        extensions: ['.js', '.jsx', '.es6', '.es', '.mjs', '.ts', '.tsx']
    }),
    terser()
];

const iife = {
    input: 'assets/scripts/index.js',
    output: {
        format: 'iife',
        file: 'dist/scripts/main.iife.min.js',
        name: 'idetik',
        globals: { jquery: '$' }
    },
    plugins: scriptsPlugin,
    external: ['jquery']
};

const admin = {
    input: 'assets/scripts/admin.js',
    output: {
        format: 'iife',
        file: 'dist/scripts/admin.iife.min.js',
        name: 'idetik',
        globals: { jquery: '$' }
    },
    plugins: scriptsPlugin,
    external: ['jquery']
};

const esm = {
    input: 'assets/scripts/index.js',
    output: {
        format: 'es',
        file: 'dist/scripts/main.esm.min.js',
        globals: { jquery: '$' }
    },
    plugins: scriptsPlugin,
    external: ['jquery']
};

switch (process.env.BABEL_ENV) {
case 'esm':
    tasks.push(esm);
    break;
case 'iife':
    tasks.push(iife);
    tasks.push(admin);
    break;
default:
    tasks.push(iife);
    tasks.push(admin);
    tasks.push(esm);
    break;
}

export default tasks;
