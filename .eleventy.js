import postcss from 'postcss';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';
import CleanCSS from 'clean-css';
import { EleventyRenderPlugin } from "@11ty/eleventy";
import EleventyPluginRobotsTxt from "eleventy-plugin-robotstxt";
// import htmlmin from "html-minifier"; // Import html-minifier
import htmlmin from "html-minifier-terser";
import { minify } from "terser";
import markdownIt from "markdown-it";

// Define robots.txt options
/** @type {import("eleventy-plugin-robotstxt/typedefs.js").EleventyPluginRobotsTxtOptions} */
const eleventyPluginRobotsTxtOptions = {
  rules: new Map([
    [
      "*",
      [
        // { disallow: "/services-terms/" },
        // { disallow: "/terms/" },
        // { disallow: "/privacy/" },
        // { disallow: "/accessibility/" },
        { disallow: "/thank-you/" },
        { disallow: "/styleguide/" },
        // { disallow: "/returns/" },
        { disallow: "/images/" }
      ],
    ],
  ]),
};

export default function configureEleventy(eleventyConfig) {
  eleventyConfig.setUseGitIgnore(false); // Disable cache from .gitignore files
  eleventyConfig.setWatchJavaScriptDependencies(false); // Disable JavaScript dependency caching
  eleventyConfig.addShortcode("year", () => `${new Date().getFullYear()}`); // Year shortcode
  eleventyConfig.addGlobalData("site", {
    url: process.env.SITE_URL || "http://localhost:8080" // Fallback URL if not set
  });

  // Create a markdown-it instance
  const markdownItInstance = new markdownIt();

  // Add a filter for converting Markdown to HTML
  eleventyConfig.addFilter("markdownify", function(content) {
    return markdownItInstance.render(content);
  });

  // Copy static assets (like images) directly
  eleventyConfig.addPassthroughCopy("src/images");

  // Put form process in root
  eleventyConfig.addPassthroughCopy({ 'src/process.php': '/process.php' });

  // Configure PostCSS with Tailwind and Autoprefixer
  eleventyConfig.addNunjucksAsyncFilter('postcss', (cssCode, done) => {
    postcss([tailwindcss(), autoprefixer()])
      .process(cssCode)
      .then(
        (r) => done(null, r.css),
        (e) => done(e, null)
      );
  });

  // Minify CSS
  eleventyConfig.addFilter("cssmin", function(code) {
    return new CleanCSS({}).minify(code).styles;
  });

  // Watch CSS files for changes
  eleventyConfig.addWatchTarget('src/styles/**/*.css');

  // Add Eleventy Render Plugin
  eleventyConfig.addPlugin(EleventyRenderPlugin);

  // Add robots.txt plugin with options
  eleventyConfig.addPlugin(EleventyPluginRobotsTxt, eleventyPluginRobotsTxtOptions);

  // JS minifcation filter
  eleventyConfig.addAsyncFilter("jsmin", async function (code) {
    try {
      const minified = await minify(code);
      return minified.code;
    } catch (err) {
      console.error("Terser error:", err);
      return code; // fail gracefully by returning unminified code
    }
  });

  // HTML minification transform
  eleventyConfig.addTransform("htmlmin", function (content) {
		if ((this.page.outputPath || "").endsWith(".html")) {
			let minified = htmlmin.minify(content, {
				useShortDoctype: true,
				removeComments: true,
				collapseWhitespace: true,
        minifyCSS: true,
        minifyJS: true
			});

			return minified;
		}

		// If not an HTML output, return content as-is
		return content;
	});
  
  return {
    dir: {
      input: 'src',
      output: 'dist',
    },
  };
}
