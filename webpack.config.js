import { resolve } from 'path';

export default {
  entry: './public/javascript/index.js',  // Entry point for your custom JS files
  output: {
    filename: 'bundle.js',  // Output file after bundling
    path: resolve('public/javascript'),  // Output directory
  },
  mode: 'development',  // Set mode (development/production)
  module: {
    rules: [
      {
        test: /\.js$/,  // All JS files
        exclude: /node_modules/,  // Exclude node_modules from transpiling
      },
    ],
  },
};
