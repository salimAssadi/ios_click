<style>
    body {
        margin: 0px;
        margin-top: 100px;
        /* Adjust this value based on your header height */
        margin-bottom: 100px;
        /* Adjust this value based on your header height */
        border: 1px solid #000;
        border-top: none;
        border-bottom: none;
        /* font-size: 12px ; */
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 100px;
        /* Adjust this value based on your header height */
        text-align: center;
        line-height: 3px;
        /* Adjust based on your header content */
        z-index: 1000;
        /* Ensure header is on top */
        font-weight: bold !important;
    }

    h1 {
        color: #333;
        text-align: center;
    }

    /* p {
        color: #666;
        text-align: center;
    } */
    .text-center {
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
        padding: 0px 1px;
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
        page-break-inside: avoid;
        padding: 10px;
    }

    .page-number:before {
        content: counter(page);
    }

    .footer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        height: 100px;
    }

    .sub-number {
        width: 30px !important;
        height: fit-content !important;

    }

    .number {
        width: 30px !important;
        height: fit-content !important;

    }

    .description {
        display:flex;
        direction: rtl; /* للنصوص العربية */
        text-emphasis-position: initial
      
    }
    .description p {
        display:flex;
        text-align: right;
        direction: rtl; /* للنصوص العربية */
    }

  

    .text-justify {
        text-align: start;
        font-size: 14pt;
    }
    .tabs * td{
        border: 0.1px rgb(150, 150, 228) solid !important;
        padding: 3px !important;
        height: fit-content !important;
        clear: both;
        text-align: right;
    }
    .tabs tr{
        padding: 1px !important;
        margin: 0px !important;
        
    }
</style>
