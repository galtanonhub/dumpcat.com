// Eleventy configuration.
// Source templates live in src/ and build to _site/ (plain static HTML).
// Day-to-day: `npm start` previews on localhost, `npm run build` builds to _site/.

module.exports = function (eleventyConfig) {
  // ISO yyyy-mm-dd, used by sitemap.njk for <lastmod>. Falls back to the build date.
  eleventyConfig.addFilter("isoDate", (d) =>
    (d ? new Date(d) : new Date()).toISOString().slice(0, 10)
  );

  // Current year, used in the footer copyright.
  eleventyConfig.addShortcode("year", () => `${new Date().getFullYear()}`);

  // --- Static assets: copied to the output untouched ---
  eleventyConfig.addPassthroughCopy("src/style.css");
  eleventyConfig.addPassthroughCopy("src/favicon.svg");
  eleventyConfig.addPassthroughCopy("src/robots.txt");

  // The 404 page ships verbatim (no templating).
  eleventyConfig.addPassthroughCopy("src/404.html");
  eleventyConfig.ignores.add("src/404.html");

  return {
    // SAMPLE STAGING: served from a subfolder while it's inventory
    // (dragonworkflows.com/samples/garage-door-repair/).
    // pathPrefix + the `| url` filter keeps every internal link correct.
    // AT LAUNCH on its own domain: change this one line to "/".
    pathPrefix: "/samples/garage-door-repair/",
    dir: {
      input: "src",
      includes: "_includes",
      data: "_data",
      output: "_site",
    },
    htmlTemplateEngine: "njk",
    markdownTemplateEngine: "njk",
  };
};
