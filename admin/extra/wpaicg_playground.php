<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
    /* Container */
    .wpaicg_form_container {
        padding: 30px;
        max-width: auto;
    }

    /* Form elements */
    .wpaicg_form_container select,
    .wpaicg_form_container textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #d1d1d1;
        border-radius: 4px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    /* Buttons */
    .wpaicg_form_container button {
        padding: 10px 15px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    .wpaicg_form_container .wpaicg_generator_button {
        background-color: #2271B1;
        color: #ffffff;
        border: none;
    }

    .wpaicg_form_container .wpaicg_generator_stop {
        background-color: #dc3232;
        color: #ffffff;
        border: none;
        display: none;
    }

    /* Spinner */
    .wpaicg_form_container .spinner {
        display: inline-block;
        visibility: hidden;
        vertical-align: middle;
        margin-left: 5px;
    }

    /* Textarea */
    .wpaicg_prompt {
        height: auto !important;
        min-height: 100px;
        resize: vertical;
    }

    /* Notice text */
    .wpaicg_notice_text_pg {
        padding: 10px;
        background-color: #F8DC6F;
        text-align: left;
        margin-bottom: 12px;
        color: #000;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }
    /* add border for table */
    .wpaicg_playground_table {
    max-width: auto;
    width: 100%;
}


</style>
<div class="wpaicg-grid-three" style="margin-top: 20px;">
    <div class="wpaicg-grid-1">
        <table>
        <tbody>
        <tr>
            <td>
                <h3><?php echo esc_html__('Category','gpt3-ai-content-generator')?></h3>
                <select id="category_select" class="regular-text">
                    <option value=""><?php echo esc_html__('Select a category','gpt3-ai-content-generator')?></option>
                    <option value="wordpress"><?php echo esc_html__('WordPress','gpt3-ai-content-generator')?></option>
                    <option value="blogging"><?php echo esc_html__('Blogging','gpt3-ai-content-generator')?></option>
                    <option value="writing"><?php echo esc_html__('Writing','gpt3-ai-content-generator')?></option>
                    <option value="ecommerce"><?php echo esc_html__('E-commerce','gpt3-ai-content-generator')?></option>
                    <option value="online_business"><?php echo esc_html__('Online Business','gpt3-ai-content-generator')?></option>
                    <option value="entrepreneurship"><?php echo esc_html__('Entrepreneurship','gpt3-ai-content-generator')?></option>
                    <option value="seo"><?php echo esc_html__('SEO','gpt3-ai-content-generator')?></option>
                    <option value="social_media"><?php echo esc_html__('Social Media','gpt3-ai-content-generator')?></option>
                    <option value="digital_marketing"><?php echo esc_html__('Digital Marketing','gpt3-ai-content-generator')?></option>
                    <option value="woocommerce"><?php echo esc_html__('WooCommerce','gpt3-ai-content-generator')?></option>
                    <option value="content_strategy"><?php echo esc_html__('Content Strategy','gpt3-ai-content-generator')?></option>
                    <option value="keyword_research"><?php echo esc_html__('Keyword Research','gpt3-ai-content-generator')?></option>
                    <option value="product_listing"><?php echo esc_html__('Product Listing','gpt3-ai-content-generator')?></option>
                    <option value="customer_relationship_management"><?php echo esc_html__('Customer Relationship Management','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
        <tr class="sample_prompts_row" style="display: none;">
            <td>
                <h3><?php echo esc_html__('Prompt','gpt3-ai-content-generator')?></h3>
                <select id="sample_prompts" class="regular-text">
                    <option value=""><?php echo esc_html__('Select a prompt','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <h3><?php echo esc_html__('Custom Prompt','gpt3-ai-content-generator')?></h3>
                <textarea type="text" class="regular-text wpaicg_prompt"><?php echo esc_html__('Write a blog post on how to effectively monetize a blog, discussing various methods such as affiliate marketing, sponsored content, and display advertising, as well as tips for maximizing revenue.','gpt3-ai-content-generator')?></textarea>
                &nbsp;<button class="button wpaicg_generator_button"><span class="spinner"></span><?php echo esc_html__('Generate','gpt3-ai-content-generator')?></button>
                &nbsp;<button class="button button-primary wpaicg_generator_stop"><?php echo esc_html__('Stop','gpt3-ai-content-generator')?></button>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
    <div class="wpaicg-grid-2">
    <?php
                wp_editor('','wpaicg_generator_result', array('media_buttons' => true, 'textarea_name' => 'wpaicg_generator_result'));
                ?>
                <p class="wpaicg-playground-buttons">
                    <button class="button button-primary wpaicg-playground-save"><?php echo esc_html__('Save as Draft','gpt3-ai-content-generator')?></button>
                    <button class="button wpaicg-playground-clear"><?php echo esc_html__('Clear','gpt3-ai-content-generator')?></button>
                </p>


    </div>
</div>

<script>
    jQuery(document).ready(function ($){
        // Define the prompts
        var prompts = [
            {category: 'wordpress', prompt: 'Write a beginner-friendly tutorial on how to set up a secure and optimized WordPress website, focusing on security measures, performance enhancements, and best practices.'},
            {category: 'wordpress', prompt: 'Create a list of essential WordPress plugins for various niches, explaining their features, use cases, and benefits for website owners.'},
            {category: 'wordpress', prompt: 'Develop an in-depth guide on how to improve the loading speed of a WordPress website, covering hosting, caching, image optimization, and more.'},
            {category: 'wordpress', prompt: 'Write an article on how to choose the perfect WordPress theme for a specific business niche, taking into account design, functionality, and customization options.'},
            {category: 'wordpress', prompt: 'Create a comprehensive guide on managing a WordPress website, including updating themes and plugins, performing backups, and monitoring site health.'},
            {category: 'wordpress', prompt: 'Write a tutorial on how to create a custom WordPress theme from scratch, covering design principles, template hierarchy, and best practices for coding.'},
            {category: 'wordpress', prompt: 'Develop a resource guide on how to leverage WordPress Multisite to manage multiple websites efficiently, including setup, management, and use cases.'},
            {category: 'wordpress', prompt: 'Write an article on the benefits of using WooCommerce for e-commerce websites, including features, extensions, and comparisons to other e-commerce platforms.'},
            {category: 'wordpress', prompt: 'Create a guide on how to optimize a WordPress website for search engines, focusing on SEO-friendly themes, plugins, and on-page optimization techniques.'},
            {category: 'wordpress', prompt: 'Write a case study on a successful WordPress website, detailing its design, growth strategies, and the impact of its content on its target audience.'},
            {category: 'blogging', prompt: 'Write a blog post on how to effectively monetize a blog, discussing various methods such as affiliate marketing, sponsored content, and display advertising, as well as tips for maximizing revenue.'},
            {category: 'blogging', prompt: 'Write a blog post about the importance of networking and collaboration in the blogging community, including practical tips for building relationships and partnering with other bloggers and influencers.'},
            {category: 'blogging', prompt: 'Create a blog post that explores various content formats for blogging, such as written articles, podcasts, and videos, and discusses their pros and cons, as well as strategies for selecting the best format for a specific audience.'},
            {category: 'blogging', prompt: 'Write a blog post detailing the essential elements of a successful blog design and layout, focusing on user experience and visual appeal.'},
            {category: 'blogging', prompt: 'Write a blog post discussing the importance of authentic storytelling in blogging and how it can enhance audience engagement and brand loyalty.'},
            {category: 'blogging', prompt: 'Write a blog post about leveraging social media for blog promotion, including tips on cross-platform marketing and strategies for increasing blog visibility.'},
            {category: 'blogging', prompt: 'Write a blog post exploring the role of search engine optimization in blogging success, with a step-by-step guide on optimizing blog content for improved search rankings.'},
            {category: 'blogging', prompt: 'Write a blog post about the value of developing a consistent posting schedule and editorial calendar, sharing strategies for maintaining productivity and audience interest.'},
            {category: 'blogging', prompt: 'Write a blog post about the benefits and challenges of embracing a lean startup methodology, with actionable tips for implementing this approach in a new business venture.'},
            {category: 'writing', prompt: 'Write an article discussing the benefits of incorporating mindfulness and meditation practices into daily routines for improved mental health.'},
            {category: 'writing', prompt: 'Write an article exploring the impact of sustainable agriculture practices on global food security and the environment.'},
            {category: 'writing', prompt: 'Write an article analyzing the role of renewable energy sources in combating climate change and reducing global carbon emissions.'},
            {category: 'writing', prompt: 'Write an article examining the history and cultural significance of traditional art forms from around the world.'},
            {category: 'writing', prompt: 'Write an article discussing the importance of financial literacy and practical tips for managing personal finances.'},
            {category: 'writing', prompt: 'Write an article highlighting advancements in telemedicine and its potential to transform healthcare access and delivery.'},
            {category: 'writing', prompt: 'Write an article discussing the ethical implications of artificial intelligence and its potential effects on society.'},
            {category: 'writing', prompt: 'Write an article exploring the benefits of lifelong learning and its impact on personal and professional growth.'},
            {category: 'writing', prompt: 'Write an article analyzing the role of urban planning and design in creating sustainable and livable cities.'},
            {category: 'writing', prompt: 'Write an article discussing the influence of technology on modern communication and its effect on human relationships.'},
            {category: 'ecommerce', prompt: 'Design a digital marketing campaign for an online fashion store, focusing on customer engagement and boosting sales.'},
            {category: 'ecommerce', prompt: 'Create a step-by-step guide for optimizing an e-commerce websites user experience, including navigation, product presentation, and checkout process.'},
            {category: 'ecommerce', prompt: 'Write a persuasive email sequence for a cart abandonment campaign, aimed at encouraging customers to complete their purchases.'},
            {category: 'ecommerce', prompt: 'Develop a content strategy for an e-commerce blog, focusing on topics that will educate, inform, and entertain potential customers.'},
            {category: 'ecommerce', prompt: 'Outline the benefits and features of a new e-commerce platform designed to simplify the process of setting up and managing an online store.'},
            {category: 'ecommerce', prompt: 'Create a video script for a product demonstration that highlights the unique selling points of an innovative kitchen gadget.'},
            {category: 'ecommerce', prompt: 'Design a customer loyalty program for an e-commerce business, focusing on rewards, incentives, and strategies to drive repeat purchases.'},
            {category: 'ecommerce', prompt: 'Write a case study showcasing the successful implementation of an e-commerce solution for a small brick-and-mortar retailer.'},
            {category: 'ecommerce', prompt: 'Develop an infographic that illustrates the growth of e-commerce, including key statistics, trends, and milestones in the industry.'},
            {category: 'ecommerce', prompt: 'Create a series of social media posts for an e-commerce brand that showcases their products and engages their target audience.'},
            {category: 'online_business', prompt: 'Create a comprehensive guide on selecting the best e-commerce platform for a new online business, considering features, pricing, and scalability.'},
            {category: 'online_business', prompt: 'Develop a social media marketing plan for a small online business, focusing on choosing the right platforms, content creation, and audience engagement.'},
            {category: 'online_business', prompt: 'Write an in-depth article on utilizing search engine optimization (SEO) strategies to drive organic traffic to an online business website.'},
            {category: 'online_business', prompt: 'Design a webinar series that teaches aspiring entrepreneurs the essentials of building and managing a successful online business.'},
            {category: 'online_business', prompt: 'Create a resource guide on the top tools and software solutions for managing an online business, covering inventory management, marketing, and customer service.'},
            {category: 'online_business', prompt: 'Write a case study about a successful online business that pivoted during challenging times and thrived through innovation and adaptability.'},
            {category: 'online_business', prompt: 'Develop a list of best practices for creating an engaging and visually appealing online business website that attracts customers and drives sales.'},
            {category: 'online_business', prompt: 'Outline a customer support strategy for an online business, focusing on communication channels, response times, and customer satisfaction.'},
            {category: 'online_business', prompt: 'Write an article on the importance of branding and visual identity for an online business, including tips for creating a consistent and memorable brand.'},
            {category: 'online_business', prompt: 'Create a guide on using email marketing to nurture leads and convert them into loyal customers for an online business.'},
            {category: 'entrepreneurship', prompt: 'Develop a step-by-step guide on how to identify and validate a profitable niche for a new business venture, including market research and competitor analysis.'},
            {category: 'entrepreneurship', prompt: 'Write an article on the most effective funding options for startups, exploring crowdfunding, angel investors, venture capital, and bootstrapping.'},
            {category: 'entrepreneurship', prompt: 'Create a comprehensive guide on building a strong team for a startup, focusing on hiring strategies, team culture, and effective communication.'},
            {category: 'entrepreneurship', prompt: 'Design a video tutorial series on creating a successful business plan, covering executive summary, market analysis, marketing strategy, and financial projections.'},
            {category: 'entrepreneurship', prompt: 'Write a case study on a successful entrepreneur who overcame significant challenges and setbacks on their journey to building a thriving business.'},
            {category: 'entrepreneurship', prompt: 'Develop a list of essential legal considerations for starting a new business, including business structure, licensing, permits, and intellectual property protection.'},
            {category: 'entrepreneurship', prompt: 'Outline a guide on how to develop and maintain a healthy work-life balance as an entrepreneur, with a focus on time management, delegation, and self-care.'},
            {category: 'entrepreneurship', prompt: 'Write an article on the importance of networking for entrepreneurs, including strategies for building connections, maintaining relationships, and leveraging partnerships.'},
            {category: 'entrepreneurship', prompt: 'Create a resource guide on top tools and technologies for startups, covering project management, communication, financial management, and customer relationship management.'},
            {category: 'entrepreneurship', prompt: 'Develop an in-depth guide on how to effectively pivot a business when faced with unexpected challenges, including recognizing the need for change and implementing a new strategy.'},
            {category: 'seo', prompt: 'Write an in-depth guide on conducting comprehensive keyword research for website content, focusing on understanding user intent, search volume, and competition.'},
            {category: 'seo', prompt: 'Develop a blog post on the essential on-page SEO factors that every website owner should know, including proper URL structures, title tags, header tags, and meta descriptions.'},
            {category: 'seo', prompt: 'Create a comprehensive guide on link-building strategies for improving website authority and search rankings, covering techniques such as guest blogging, broken link building, and outreach.'},
            {category: 'seo', prompt: 'Write an article about the impact of website speed on SEO and user experience, discussing tools and techniques for analyzing and improving site performance.'},
            {category: 'seo', prompt: 'Develop a tutorial on how to create SEO-friendly content that appeals to both search engines and human readers, focusing on readability, keyword usage, and information value.'},
            {category: 'seo', prompt: 'Write a blog post about the importance of mobile-first indexing and responsive web design in modern SEO, including tips for optimizing websites for mobile devices.'},
            {category: 'seo', prompt: 'Create a guide on how to use Google Search Console effectively for monitoring and improving website SEO performance, including features such as index coverage reports, sitemaps, and search analytics.'},
            {category: 'seo', prompt: 'Write an article discussing the role of voice search in SEO, highlighting strategies for optimizing website content for voice search queries and emerging trends in voice search technology.'},
            {category: 'seo', prompt: 'Develop a blog post about the significance of user experience (UX) in SEO, including tips for enhancing website navigation, layout, and overall user satisfaction to improve search rankings.'},
            {category: 'seo', prompt: 'Create an article on the importance of local SEO for small businesses, focusing on strategies such as Google My Business optimization, citation building, and local content creation.'},
            {category: 'social_media', prompt: 'Write an article on the most effective strategies for growing a brands presence on social media platforms, including content creation, engagement, and advertising.'},
            {category: 'social_media', prompt: 'Develop a blog post about the benefits of using social media analytics to improve marketing efforts, with tips on interpreting data and making data-driven decisions.'},
            {category: 'social_media', prompt: 'Create a guide on how to create compelling visual content for social media platforms, focusing on elements such as color, typography, and composition.'},
            {category: 'social_media', prompt: 'Craft an in-depth article on leveraging user-generated content to boost brand authenticity and increase engagement on social media platforms.'},
            {category: 'social_media', prompt: 'Write a comprehensive tutorial on optimizing social media profiles for search engines, highlighting the importance of keywords, descriptions, and profile images.'},
            {category: 'social_media', prompt: 'Develop an informative blog post about the role of social media influencers in brand promotion, and outline the process of selecting and collaborating with the right influencers for a specific target audience.'},
            {category: 'social_media', prompt: 'Create a guide on how to effectively use social media scheduling tools to streamline content creation and posting, ensuring consistency and maximizing reach.'},
            {category: 'social_media', prompt: 'Write an article discussing the best practices for managing online communities on social media platforms, focusing on fostering positive interactions and handling negative feedback.'},
            {category: 'social_media', prompt: 'Develop a blog post about the importance of storytelling in social media marketing, with tips on creating engaging narratives that resonate with audiences and generate brand loyalty.'},
            {category: 'social_media', prompt: 'Create a guide on how to measure and analyze the return on investment (ROI) for social media advertising campaigns, including the key performance indicators (KPIs) to track and optimize.'},
            {category: 'digital_marketing', prompt: 'Write a comprehensive guide on creating and executing a successful content marketing strategy, including planning, creation, distribution, and measurement.'},
            {category: 'digital_marketing', prompt: 'Develop a blog post about the benefits of using marketing automation tools, with examples of popular platforms and use cases for different business sizes and industries.'},
            {category: 'digital_marketing', prompt: 'Create an article discussing the role of influencer marketing in modern advertising, with tips on selecting the right influencers, developing campaigns, and measuring success.'},
            {category: 'digital_marketing', prompt: 'Write an in-depth guide on utilizing search engine optimization (SEO) techniques for improving website visibility, including keyword research, on-page optimization, and off-page strategies.'},
            {category: 'digital_marketing', prompt: 'Craft a detailed article on the importance of social media marketing, highlighting effective platform-specific strategies, content planning, engagement techniques, and performance analysis.'},
            {category: 'digital_marketing', prompt: 'Develop a comprehensive guide on email marketing best practices, covering list building, segmentation, email design, personalization, automation, and metrics tracking.'},
            {category: 'digital_marketing', prompt: 'Write an informative blog post about the advantages of data-driven marketing, including insights on collecting, analyzing, and applying data to enhance targeting, personalization, and campaign effectiveness.'},
            {category: 'digital_marketing', prompt: 'Create an article exploring the benefits of video marketing, with tips on producing engaging content, optimizing for search engines, and leveraging various distribution channels.'},
            {category: 'digital_marketing', prompt: 'Write a guide on implementing effective pay-per-click (PPC) advertising campaigns, discussing budget allocation, keyword targeting, ad copywriting, landing page optimization, and performance analysis.'},
            {category: 'digital_marketing', prompt: 'Develop a blog post on the role of content repurposing in digital marketing, providing strategies for transforming existing content into different formats and leveraging multiple distribution channels.'},
            {category: 'woocommerce', prompt: 'Write a comprehensive guide on optimizing WooCommerce stores for maximum performance, discussing topics such as caching, image optimization, database cleaning, and choosing the right hosting environment.'},
            {category: 'woocommerce', prompt: 'Create an in-depth tutorial on setting up a successful WooCommerce store from scratch, covering aspects like choosing the right theme, setting up payment gateways, configuring shipping options, and managing inventory.'},
            {category: 'woocommerce', prompt: 'Develop an article on the top WooCommerce plugins that can enhance an online store’s functionality, covering areas such as analytics, email marketing, product recommendations, and customer support.'},
            {category: 'woocommerce', prompt: 'Write a detailed guide on implementing effective WooCommerce SEO strategies to improve search engine visibility, discussing on-page optimization, product schema markup, permalink structure, and sitemaps.'},
            {category: 'woocommerce', prompt: 'Craft an article on enhancing the user experience of a WooCommerce store, focusing on design principles, seamless navigation, product presentation, mobile responsiveness, and checkout optimization.'},
            {category: 'woocommerce', prompt: 'Write an in-depth article on maximizing sales and conversions for WooCommerce stores, covering strategies such as abandoned cart recovery, personalized product recommendations, and utilizing customer reviews.'},
            {category: 'woocommerce', prompt: 'Create a comprehensive guide on managing and scaling a WooCommerce store, discussing topics like inventory management, order fulfillment, automating processes, and expanding into new markets.'},
            {category: 'woocommerce', prompt: 'Develop an article on the importance of security for WooCommerce stores and best practices to protect against threats, including SSL certificates, secure hosting, regular backups, and security plugins.'},
            {category: 'woocommerce', prompt: 'Write a detailed tutorial on how to create and implement a successful marketing strategy for a WooCommerce store, covering email marketing, social media advertising, content marketing, and influencer partnerships.'},
            {category: 'woocommerce', prompt: 'Craft an article on the benefits of integrating third-party services and APIs with a WooCommerce store, focusing on areas such as payment processing, shipping solutions, marketing automation, and customer relationship management.'},
            {category: 'content_creation', prompt: 'Write an in-depth guide on brainstorming and developing unique content ideas, covering various research methods, mind mapping, and using audience feedback to inform content creation.'},
            {category: 'content_creation', prompt: 'Develop a comprehensive article on the principles of effective copywriting, focusing on techniques such as writing compelling headlines, utilizing storytelling, and creating persuasive calls to action.'},
            {category: 'content_creation', prompt: 'Create a detailed tutorial on how to structure and format long-form content for maximum readability and engagement, discussing elements like headings, lists, images, and content flow.'},
            {category: 'content_creation', prompt: 'Craft a blog post about the role of visual storytelling in content creation, with tips on using images, videos, and infographics to enhance the impact of written content and engage diverse audiences.'},
            {category: 'content_creation', prompt: 'Write an informative guide on optimizing content for search engines, including keyword research, proper use of headings and meta tags, internal and external linking, and image optimization.'},
            {category: 'content_creation', prompt: 'Develop an article discussing the importance of editorial calendars in content creation, covering aspects like planning, organization, collaboration, and ensuring consistent content output.'},
            {category: 'content_creation', prompt: 'Write a comprehensive guide on using various multimedia formats in content creation, such as podcasts, webinars, and interactive content, to cater to different audience preferences and enhance engagement.'},
            {category: 'content_creation', prompt: 'Create a blog post about the role of user-generated content in content marketing, with tips on encouraging audience participation, curating submissions, and leveraging this content for promotional purposes.'},
            {category: 'content_creation', prompt: 'Craft a detailed article on repurposing existing content for different platforms and formats, such as transforming blog posts into infographics, videos, or social media snippets to maximize reach and engagement.'},
            {category: 'content_creation', prompt: 'Write an in-depth tutorial on incorporating storytelling techniques into content creation, including character development, conflict resolution, and narrative structure to create engaging and memorable content.'},
            {category: 'content_strategy', prompt: 'Write a comprehensive guide on creating a data-driven content strategy, including audience research, content gap analysis, setting goals, and measuring success through key performance indicators.'},
            {category: 'content_strategy', prompt: 'Develop a blog post discussing the importance of evergreen content in a content strategy, providing examples and tips on how to create timeless and valuable pieces that continue to drive traffic and engagement.'},
            {category: 'content_strategy', prompt: 'Create an in-depth article on the role of content distribution in a successful content strategy, covering various channels such as social media, email, guest posting, and leveraging partnerships to maximize reach.'},
            {category: 'content_strategy', prompt: 'Craft a detailed guide on using user personas to inform content strategy, discussing the process of creating accurate personas, identifying their needs and pain points, and tailoring content to address their specific interests.'},
            {category: 'content_strategy', prompt: 'Write an informative blog post about the benefits of conducting a content audit, outlining the steps involved, identifying underperforming content, and implementing improvements to enhance overall content strategy effectiveness.'},
            {category: 'content_strategy', prompt: 'Develop a comprehensive article on the importance of a well-defined content calendar for a successful content strategy, including tips on planning, organization, consistency, and collaboration among team members.'},
            {category: 'content_strategy', prompt: 'Write an in-depth guide on leveraging content analytics to improve content strategy, discussing key metrics to track, data-driven decision-making, and using insights to optimize content performance and audience engagement.'},
            {category: 'content_strategy', prompt: 'Create a detailed blog post about the role of content curation in a content strategy, with tips on sourcing high-quality content, adding value through commentary, and sharing curated pieces to supplement original content.'},
            {category: 'content_strategy', prompt: 'Craft an informative article on incorporating different content formats and types into a content strategy, such as blog posts, case studies, whitepapers, videos, and podcasts, to cater to diverse audience preferences.'},
            {category: 'content_strategy', prompt: 'Write a comprehensive guide on effective content promotion techniques to boost visibility and engagement, covering strategies such as influencer outreach, social media advertising, and search engine optimization.'},
            {category: 'keyword_research', prompt: 'Write a comprehensive guide on the basics of keyword research, explaining its importance in SEO, the tools used, and how it influences content creation and website ranking.'},
            {category: 'keyword_research', prompt: 'Craft an in-depth blog post about long-tail keywords, discussing their role in modern SEO strategies, how to identify them, and techniques for effectively incorporating them into content.'},
            {category: 'keyword_research', prompt: 'Develop a detailed tutorial on using Google Keyword Planner for keyword research, including step-by-step instructions and tips for interpreting and applying the data.'},
            {category: 'keyword_research', prompt: 'Create an informative article on the relationship between keyword research and user intent, explaining how understanding the latter can guide the former to produce more targeted, effective content.'},
            {category: 'keyword_research', prompt: 'Compose a comprehensive guide on the role of competitor analysis in keyword research, detailing how to identify and evaluate the keyword strategies of successful competitors.'},
            {category: 'keyword_research', prompt: 'Write an in-depth article on the integration of keyword research into content strategy, explaining how to seamlessly embed keywords into various types of content for maximum SEO impact.'},
            {category: 'keyword_research', prompt: 'Craft an informative piece on the importance of keyword relevancy and search volume in keyword research, and discuss how to balance these factors for optimal results.'},
            {category: 'keyword_research', prompt: 'Develop a detailed tutorial on tracking and refining keyword performance over time, including the tools and metrics that can be used to measure success.'},
            {category: 'keyword_research', prompt: 'Create an insightful blog post about the pitfalls to avoid in keyword research, discussing common mistakes and misconceptions that can hinder SEO efforts.'},
            {category: 'product_listing', prompt: 'Create an exhaustive guide on designing a product listing page that maximizes conversion rates, focusing on product descriptions, images, customer reviews, and pricing.'},
            {category: 'product_listing', prompt: 'Develop a detailed article on optimizing product listings for search engines, considering keyword research, SEO-friendly URLs, and meta descriptions.'},
            {category: 'product_listing', prompt: 'Craft a webinar series that provides insights into A/B testing for product listings, teaching businesses how to refine their listings based on customer behavior.'},
            {category: 'product_listing', prompt: 'Write a case study on a successful online retailer that significantly increased sales through effective product listing strategies, outlining the steps they took and the results they achieved.'},
            {category: 'product_listing', prompt: 'Develop a list of best practices for managing product listings on multiple e-commerce platforms, focusing on maintaining consistency, updating inventory, and handling customer queries.'},
            {category: 'product_listing', prompt: 'Create a comprehensive guide on how to write persuasive product descriptions that effectively showcase the benefits and features of products, leading to increased sales.'},
            {category: 'product_listing', prompt: 'Develop a blog post discussing the role of high-quality imagery and video content in product listings, including tips on product photography and videography.'},
            {category: 'product_listing', prompt: 'Design a tutorial on using data analytics to improve product listings, focusing on understanding customer behavior, product performance, and sales trends.'},
            {category: 'product_listing', prompt: 'Write a case study on a business that used cross-selling and up-selling techniques within their product listings to increase average order value, discussing the strategy and the results.'},
            {category: 'product_listing', prompt: 'Outline a strategy for managing product listings during peak sales periods, such as holidays or sales events, considering inventory management, pricing adjustments, and customer service.'},
            {category: 'customer_relationship_management', prompt: 'Compose an in-depth guide explaining how a CRM system can transform customer service operations, detailing the features and tools that can streamline customer interactions and enhance customer satisfaction.'},
            {category: 'customer_relationship_management', prompt: 'Write a comprehensive article on the role of analytics in CRM, explaining how businesses can leverage data to understand customer behavior, predict future trends, and personalize customer interactions.'},
            {category: 'customer_relationship_management', prompt: 'Develop a detailed tutorial on integrating a CRM system into a companys sales and marketing strategies, including practical steps and potential challenges to anticipate.'},
            {category: 'customer_relationship_management', prompt: 'Create an insightful blog post about the importance of CRM in retaining customers and building long-term relationships, discussing strategies for using CRM to increase customer loyalty and lifetime value.'},
            {category: 'customer_relationship_management', prompt: 'Craft a case study showcasing a successful implementation of a CRM system in a business, highlighting the benefits it brought in terms of sales growth, customer satisfaction, and improved internal processes.'},
            {category: 'customer_relationship_management', prompt: 'Write an exhaustive guide on choosing the right CRM system for a small business, taking into account factors such as scalability, usability, and integration with existing systems.'},
            {category: 'customer_relationship_management', prompt: 'Develop an in-depth article discussing the impact of AI and machine learning on CRM systems, and how these technologies can enhance customer interactions and provide deeper insights.'},
            {category: 'customer_relationship_management', prompt: 'Create a detailed tutorial on how to train employees to effectively use a CRM system, including tips for overcoming resistance to new technology.'},
            {category: 'customer_relationship_management', prompt: 'Craft an insightful blog post about the role of CRM in e-commerce, discussing how it can help online businesses better understand their customers and personalize their shopping experience.'},
            {category: 'customer_relationship_management', prompt: 'Compose a case study analyzing the transformation of a companys customer service operations before and after implementing a CRM system, detailing the challenges faced and the results achieved.'}
        ];
        // Function to handle category selection
        $('#category_select').on('change', function() {
            var selectedCategory = $(this).val();
            if (selectedCategory) {
                // Clear and populate the prompts dropdown
                $('#sample_prompts').html('<option value=""><?php echo esc_html__('Select a prompt','gpt3-ai-content-generator')?></option>');
                prompts.forEach(function(promptObj) {
                    if (promptObj.category === selectedCategory) {
                        $('#sample_prompts').append('<option value="' + promptObj.prompt + '">' + promptObj.prompt + '</option>');
                    }
                });
                $('.sample_prompts_row').show();
            } else {
                // Hide the prompts dropdown and clear its value
                $('.sample_prompts_row').hide();
                $('#sample_prompts').val('');
            }
        });

        // Function to handle sample prompt selection
        $('#sample_prompts').on('change', function() {
            var selectedPrompt = $(this).val();
            if (selectedPrompt) {
                // Clear the textarea and set the selected prompt
                $('.wpaicg_prompt').val(selectedPrompt);
            }
        });
        var wpaicg_generator_working = false;
        var eventGenerator = false;
        var wpaicg_limitLines = 1;
        function stopOpenAIGenerator(){
            $('.wpaicg-playground-buttons').show();
            $('.wpaicg_generator_stop').hide();
            wpaicg_generator_working = false;
            $('.wpaicg_generator_button .spinner').hide();
            $('.wpaicg_generator_button').removeAttr('disabled');
            eventGenerator.close();
        }
        $('.wpaicg_generator_button').click(function(){
            var btn = $(this);
            var title = $('.wpaicg_prompt').val();
            if(!wpaicg_generator_working && title !== ''){
                var count_line = 0;
                var wpaicg_generator_result = $('.wpaicg_generator_result');
                btn.attr('disabled','disabled');
                btn.find('.spinner').show();
                btn.find('.spinner').css('visibility','unset');
                wpaicg_generator_result.val('');
                wpaicg_generator_working = true;
                $('.wpaicg_generator_stop').show();
                eventGenerator = new EventSource('<?php echo esc_html(add_query_arg('wpaicg_stream','yes',site_url().'/index.php'));?>&title='+title+'&nonce=<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>');
                var editor = tinyMCE.get('wpaicg_generator_result');
                var basicEditor = true;
                if ( $('#wp-wpaicg_generator_result-wrap').hasClass('tmce-active') && editor ) {
                    basicEditor = false;
                }
                var currentContent = '';
                var wpaicg_newline_before = false;
                var wpaicg_response_events = 0;
                eventGenerator.onmessage = function (e) {
                    if(basicEditor){
                        currentContent = $('#wpaicg_generator_result').val();
                    }
                    else{
                        currentContent = editor.getContent();
                        currentContent = currentContent.replace(/<\/?p(>|$)/g, "");
                    }
                    if(e.data === "[DONE]"){
                        count_line += 1;
                        if(basicEditor) {
                            $('#wpaicg_generator_result').val(currentContent+'\n\n');
                        }
                        else{
                            editor.setContent(currentContent+'\n\n');
                        }
                        wpaicg_response_events = 0;
                    }
                    else{
                        var result = JSON.parse(e.data);
                        if(result.error !== undefined){
                            var content_generated = result.error.message;
                        }
                        else{
                            var content_generated = result.choices[0].delta !== undefined ? (result.choices[0].delta.content !== undefined ? result.choices[0].delta.content : '') : result.choices[0].text;
                        }
                        if((content_generated === '\n' || content_generated === ' \n' || content_generated === '.\n' || content_generated === '\n\n' || content_generated === '.\n\n') && wpaicg_response_events > 0 && currentContent !== ''){
                            if(!wpaicg_newline_before) {
                                wpaicg_newline_before = true;
                                if(basicEditor){
                                    $('#wpaicg_generator_result').val(currentContent+'<br /><br />');
                                }
                                else{
                                    editor.setContent(currentContent+'<br /><br />');
                                }
                            }
                        }
                        else if(content_generated === '\n' && wpaicg_response_events === 0  && currentContent === ''){

                        }
                        else{
                            wpaicg_newline_before = false;
                            wpaicg_response_events += 1;
                            if(basicEditor){
                                $('#wpaicg_generator_result').val(currentContent+content_generated);
                            }
                            else{
                                editor.setContent(currentContent+content_generated);
                            }
                        }
                    }
                    if(count_line === wpaicg_limitLines){
                        stopOpenAIGenerator();
                    }
                };
                eventGenerator.onerror = function (e) {
                };
            }
        });
        $('.wpaicg_generator_stop').click(function (){
            stopOpenAIGenerator();
        });
        $('.wpaicg-playground-clear').click(function (){
            // $('.wpaicg_prompt').val('');
            var editor = tinyMCE.get('wpaicg_generator_result');
            var basicEditor = true;
            if ( $('#wp-wpaicg_generator_result-wrap').hasClass('tmce-active') && editor ) {
                basicEditor = false;
            }
            if(basicEditor){
                $('#wpaicg_generator_result').val('');
            }
            else{
                editor.setContent('');
            }
        });
        $('.wpaicg-playground-save').click(function (){
            var wpaicg_draft_btn = $(this);
            var title = $('.wpaicg_prompt').val();
            var editor = tinyMCE.get('wpaicg_generator_result');
            var basicEditor = true;
            if ( $('#wp-wpaicg_generator_result-wrap').hasClass('tmce-active') && editor ) {
                basicEditor = false;
            }
            var content = '';
            if (basicEditor){
                content = $('#wpaicg_generator_result').val();
            }
            else{
                content = editor.getContent();
            }
            if(title === ''){
                alert('<?php echo esc_html__('Please enter title','gpt3-ai-content-generator')?>');
            }
            else if(content === ''){
                alert('<?php echo esc_html__('Please wait content generated','gpt3-ai-content-generator')?>');
            }
            else{
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {title: title, content: content, action: 'wpaicg_save_draft_post_extra','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicg_draft_btn.attr('disabled','disabled');
                        wpaicg_draft_btn.append('<span class="spinner"></span>');
                        wpaicg_draft_btn.find('.spinner').css('visibility','unset');
                    },
                    success: function (res){
                        wpaicg_draft_btn.removeAttr('disabled');
                        wpaicg_draft_btn.find('.spinner').remove();
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('post.php')?>?post='+res.id+'&action=edit';
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicg_draft_btn.removeAttr('disabled');
                        wpaicg_draft_btn.find('.spinner').remove();
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                });
            }
        })
    })
</script>
