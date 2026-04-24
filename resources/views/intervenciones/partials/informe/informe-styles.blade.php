<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12px;
        line-height: 1.6;
        color: #333;
        background: #f5f5f5;
        padding: 20px;
    }

    .preview-container {
        max-width: 210mm;
        margin: 0 auto;
        background: white;
        padding: 30px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .preview-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #0e188a;
    }

    .preview-header img {
        max-height: 80px;
        margin-bottom: 10px;
    }

    .preview-header h1 {
        color: #0e188a;
        margin: 10px 0;
        font-size: 24px;
    }

    .preview-header small {
        color: #666;
        font-size: 14px;
    }

    .preview-actions {
        position: sticky;
        top: 0;
        background: white;
        padding: 10px;
        margin: -30px -30px 30px -30px;
        border-bottom: 2px solid #0e188a;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 100;
    }

    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 9px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #0e188a;
        color: white;
    }

    .btn-primary:hover {
        background: #0a1266;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .totals-box {
        background: #f8f9fa;
        padding: 15px;
        border: 1px solid #dee2e6;
        border-left: 4px solid #0e188a;
        margin: 20px 0;
        border-radius: 4px;
    }

    .totals-box strong {
        color: #0e188a;
        font-size: 16px;
    }


    h1 {
        font-size: 18px;
        margin-bottom: 5px;
        color: #2b343a;
    }
    
    h2 {
        color: #0e188a;
        margin-bottom: 15px;
        font-size: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    h3 {
        font-size: 14px;
        font-weight: normal;
        margin-top: 0;
    }

    h4 {
        font-size: 12px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 3px;
        margin-top: 10px;
    }

    h5 {
        font-size: 10px;
        padding-bottom: 3px;
        margin-top: 10px;
    }


    table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        background: white;
        font-size: 12px;
        table-layout: fixed;
    }

    th,
    td {
        border: 1px solid #dee2e6;
        padding: 12px;
        text-align: left;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    th {
        background: #0e188a;
        color: white;
        font-weight: bold;
        text-align: left;
        font-size: 10px;
    }

    td {
        font-size: 10px;
    }

    tr:nth-child(even) {
        background: #f8f9fa;
    }

    tr:hover {
        background: #e9ecef;
    }


    .page-break {
        margin: 40px 0;
        border-top: 2px dashed #dee2e6;
        page-break-after: always;
    }


    .portada {
        position: relative;
        height: 1000px;
        /* Altura fija aproximada para A4 a 72dpi */
        width: 100%;
        border: 1px solid #eee;
        /* El recuadro que mencionaste */
    }

    .contenido-portada {
        position: absolute;
        top: 45%;
        /* Ajustado para que quede un poco más arriba de la mitad */
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        text-align: center;
    }

    .logo-portada {
        max-height: 100px;
        margin-bottom: 20px;
    }

    .seccion-info {
        margin-top: 30px;
        text-align: left;
        width: 85%;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.4;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #6c757d;
    }



    .text-right {
        text-align: right;
    }


    .contenido-html {
        font-size: 14px;
        line-height: 1.8;
    }

    .contenido-html p {
        margin-bottom: 10px;
    }

    .contenido-html ul {
        margin-left: 20px;
    }

    .conclusiones-section {
        margin-top: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-left: 4px solid #0e188a;
        border-radius: 4px;
    }

    .conclusiones-section h2 {
        margin-top: 0;
    }

    .conclusiones-section p {
        white-space: pre-wrap;
        line-height: 1.8;
    }

    .conclusiones-section strong {
        font-weight: bold;
        color: #0e188a;
    }

    .conclusiones-section em {
        font-style: italic;
    }

    .conclusiones-section ul,
    .conclusiones-section ol {
        margin: 10px 0;
        padding-left: 30px;
        line-height: 1.8;
    }

    .conclusiones-section li {
        margin: 5px 0;
    }

    .conclusiones-section h3,
    .conclusiones-section h4 {
        color: #0e188a;
        margin-top: 20px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .conclusiones-section p {
        margin: 10px 0;
        line-height: 1.8;
    }

    .footer-info {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
        text-align: center;
        color: #666;
        font-size: 12px;
    }

    @media print {
        body {
            background: white;
            padding: 0;
        }

        .preview-container {
            box-shadow: none;
            padding: 20px;
        }

        .preview-actions {
            display: none;
        }
    }

    /* Añadir a informe-styles.blade.php */
    .portada {
        height: 90vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        border: 1px solid #eee;
    }

    .portada h1 {
        font-size: 32px;
        margin-top: 20px;
        color: #0e188a;
        text-transform: uppercase;
    }

    .resumen-ejecutivo {
        background: #fdfdfd;
        padding: 25px;
        border: 1px solid #e3e6f0;
        margin-bottom: 30px;
        border-radius: 8px;
    }

    .badge-custom {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: bold;
    }

    .firma-section {
        margin-top: 50px;
        display: flex;
        justify-content: space-around;
    }

    .firma-box {
        border-top: 1px solid #333;
        width: 200px;
        text-align: center;
        padding-top: 10px;
        font-size: 12px;
    }
</style>
