import env from 'postcss-preset-env'
import path from 'path';
import postcss from 'rollup-plugin-postcss';

export default {
    input: 'sass/input.js',
    output: {
        file: 'www/style.js',
        format: 'esm'
    },
    watch: {
        include: 'sass/**.scss'
    },
    plugins: [
        postcss({
            plugins: [env()],
            extract: path.resolve('www/cryptography-demo.css'),
            minimize: false,
            use: [
                ['sass', {
                    includePaths: [
                        './sass'
                    ]
                }]
            ]
        })
    ]
}