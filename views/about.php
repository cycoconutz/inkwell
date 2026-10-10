<?php
page_title('About Inkwell');
page_meta('description', 'A self-contained PHP publishing engine built for clarity and deployability.');
?>
<section class="page">
    <h1 class="page__title">About Inkwell</h1>
    <p>
        Inkwell is a deliberately small CMS scaffold written in vanilla PHP. No third-party packages,
        no heavy framework, and no runtime Composer dependencies required to read and understand it.
    </p>
    <p>
        The architecture is intentional: a front controller, a small router, a PDO wrapper, a view/layout
        system, CSRF protection, rate-limiting against brute force, and a safe Markdown renderer.
        Everything is built to run in a single Docker container on Render.
    </p>
    <ul class="checklist">
        <li>PDO with prepared statements; no string concatenation into SQL</li>
        <li>All HTML escaped at the single boundary (<code>e()</code> helper)</li>
        <li>CSRF tokens on state-changing forms; sessions hardened</li>
        <li>Fenced PHP blocks, strict types throughout</li>
        <li>Health endpoint (<code>/healthz</code>) for platform checks</li>
    </ul>
    <p>
        This project exists to fill a demonstrable PHP/WordPress-ready scaffold for job applications,
        while still showcasing the care I take with correctness, testing and deployability.
    </p>
    <a class="btn btn--primary" href="<?= e(url('blog')) ?>">Browse the writing</a>
</section>
