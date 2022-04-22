<script>
    "use strict";

    var link = document.getElementById("themeLink");

    function changeTheme(){
        let lightTheme = "css/light.css?<? echo $v ?>";
        let darkTheme = "css/dark.css?<? echo $v ?>";
        var theme = "";

        if(link.getAttribute("href") == lightTheme){
            link.setAttribute("href", darkTheme);
            theme = "dark";
        } else {
            link.setAttribute("href", lightTheme);
            theme = "light";
        }

        saveTheme(theme);
    }

    function saveTheme(theme){
        var Request = new XMLHttpRequest();
        Request.open("GET", "include/theme.php?theme=" + theme, true);
        Request.send();
    }

    function openDownload(){
        document.getElementById("downloadBlock").classList.remove("hide");
    }
    
    function closeDownload(){
        document.getElementById("downloadBlock").classList.add("hide");
    }
</script>