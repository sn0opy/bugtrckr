<F3:include href="header.tpl.php" />
<F3:check if="{@FAILURE}">
    <F3:true>
        <h2>Error</h2>
        <div id="failure">
            <p>{@FAILURE}</p>
        </div>
    </F3:true>
    <F3:false>
        <F3:include href="{@template}" />
    </F3:false>
</F3:check>
<F3:include href="footer.tpl.php" />
