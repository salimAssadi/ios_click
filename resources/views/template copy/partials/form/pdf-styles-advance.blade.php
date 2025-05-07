<style>
    /* @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Noto+Kufi+Arabic:wght@100..900&display=swap'); */
    body {
        /* margin-top: 130px; */
        /* margin-bottom: 80px; */
        border: 5px double #393bc7 !important;
        border-top: none;
        border-bottom: none;
        direction: rtl !important;
        font-size: 15px ;
        font-family: "almarai", sans-serif;
    }
   
    body * {
        direction: rtl !important;

    }
   header {
    font-weight: bold !important;
    }
    header tr{
        border: 4px double #393bc7;
    }
  
    h1 {
        color: #333;
        text-align: center;
    }

    /* p {
        color: #666;
        text-align: center;
    } */
    header .text-center {
        text-align: center;
    }

    .text-startt {
        text-align: right !important;
    }

    ul {
        list-style-type: none;
        padding-left: 20px;
    }

    img {
        max-width: 100%;
        height: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid;
        padding: 5px;
        text-align: center;
        white-space: nowrap;
        /* line-height: 20px; */
    }
    .headerthtd {
        border: 1px solid;
        padding: 5px;
        text-align: center;
    }

    hr {
        border: 0;
        border-top: 1px solid #000;
    }

    .container {
        width: 100%;
        margin: 0 auto;
        padding: 20px;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .mt-3 {
        margin-top: 1rem;
    }

    main {
        page-break-inside: auto !important;
        padding: 10px;
        margin-top: 20px;
    }

    .page-number:before {
        content: counter(page);
    }

   /* .footer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        height: 80px;
    }*/
    footer tr{
        border: 4px double #393bc7;
    }

    .sub-number {
        width: 50px !important;
        /* height: fit-content !important; */

    }
    
    .number {
        width: 50px !important;
        /* height: fit-content !important; */

    }

    .description {
        display:flex;
        direction: rtl; /* للنصوص العربية */
        /* text-emphasis-position: initial */
        text-align: right;
        padding: 10px;

    }
    .description p {
        display:flex;
        text-align: right;
        /* white-space: pre-wrap;  */


    }
    .text-justify {
        text-align: start;
        font-size: 14pt;
    }
    .tabs * td{
        border: 0.1px rgb(150, 150, 228) solid !important;
        text-align: right !important;
    }

    #forms-table * td{
        /* border: 0.1px rgb(150, 150, 228) solid !important; */
        /* padding: 3px !important; */
        /* height: fit-content !important; */
        /* clear: both; */
        text-align: right;
        padding: 10px;

    }
    
    .squen{
        width: 30px;
    }
    @page {
        header: page-header;
        footer: page-footer;
    }

    /* header td{
        
        border: 4px double #393bc7;
    } */
</style>
