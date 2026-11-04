<style>
    .cms-note { background: #FBF0DA; border: 1px solid #F0DDB0; color: #6B4A12; border-radius: 12px; padding: 12px 16px; font-size: 13.5px; line-height: 1.55; margin-bottom: 18px; }
    .cms-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 18px; }
    .cms-card { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 22px; }
    .cms-card__icon { width: 46px; height: 46px; border-radius: 12px; background: #F3EFE6; color: #C9974D; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 14px; }
    .cms-card h3 { font-size: 17px; font-weight: 800; color: #123A2E; margin: 0 0 6px; }
    .cms-card p { font-size: 13.5px; color: #5B6560; line-height: 1.55; margin: 0 0 16px; }
    .cms-card small { color: #8A938E; }
    .cms-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .cms-btn { display: inline-flex; align-items: center; gap: 7px; height: 40px; padding: 0 16px; border-radius: 10px; border: 1px solid #DDD5C2; background: #fff; color: #123A2E !important; font-size: 13px; font-weight: 700; text-decoration: none !important; cursor: pointer; white-space: nowrap; }
    .cms-btn:hover { background: #F6F4EF; }
    .cms-btn--dark { background: #123A2E; border-color: #123A2E; color: #fff !important; }
    .cms-btn--dark:hover { background: #0D2C23; }
    .cms-btn--gold { background: #C9974D; border-color: #C9974D; color: #fff !important; }
    .cms-btn--gold:hover { background: #B88640; }
    .cms-btn--danger { color: #B02A47 !important; border-color: #F0C4CD; }
    .cms-btn--danger:hover { background: #FDEEF1; }
    .cms-form { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; padding: 26px 28px 0; max-width: none; }
    .cms-savebar { position: sticky; bottom: 0; z-index: 7; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: #fff; border-top: 1px solid #E7E2D6; margin: 0 -28px; padding: 14px 28px; border-radius: 0 0 16px 16px; box-shadow: 0 -8px 18px rgba(15,46,36,.05); }
    .cms-form label { display: block; font-size: 13px; font-weight: 700; color: #123A2E; margin: 0 0 7px; }
    .cms-form input[type=text] { width: 100%; height: 46px; border: 1px solid #DDD5C2; border-radius: 10px; padding: 0 14px; font-size: 14.5px; font-family: inherit; }
    .cms-form input[type=text]:focus { outline: none; border-color: #C9974D; box-shadow: 0 0 0 3px rgba(201,151,77,.18); }
    .cms-field { margin-bottom: 20px; }
    .cms-err { color: #B5674A; font-size: 12.5px; margin-top: 6px; }
    .cms-bar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
    .cms-check { display: inline-flex !important; align-items: center; gap: 10px; font-size: 14px; line-height: 1.3; color: #1C2321; cursor: pointer; margin: 0 !important; font-weight: 600; }
    .cms-check input { width: 18px; height: 18px; accent-color: #123A2E; margin: 0; flex: none; }
    .cms-form input::placeholder { color: #9AA5A0; }
    /* Tables: the admin theme has a global "table * { padding: 10px !important }" rule, so every inner element is reset explicitly. */
    .cms-table-wrap { background: #fff; border: 1px solid #E7E2D6; border-radius: 16px; overflow-x: auto; }
    .cms-table { width: 100%; min-width: 920px; border-collapse: collapse; table-layout: auto; }
    .cms-table th { background: #F6F4EF; color: #123A2E; font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; text-align: left; padding: 13px 16px !important; white-space: nowrap; }
    .cms-table td { padding: 14px 16px !important; border-bottom: 1px solid #F0ECE1; font-size: 13.5px; line-height: 1.45; vertical-align: middle !important; }
    .cms-table tbody tr:last-child td { border-bottom: 0; }
    .cms-table tbody tr:hover td { background: #FCFAF5; }
    .cms-table td span, .cms-table td a, .cms-table td button, .cms-table td form, .cms-table td div, .cms-table td small, .cms-table td b, .cms-table td br, .cms-table td i { padding: 0 !important; }
    .cms-table td br { display: none; }
    .cms-table td small { display: block; margin-top: 3px; }
    .cms-table .cms-num { width: 52px; color: #8A938E; font-weight: 700; }
    .cms-title { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .cms-title b { color: #123A2E; font-size: 14px; }
    .cms-table .cms-pill { display: inline-block; font-size: 11.5px; font-weight: 700; line-height: 1; padding: 6px 12px !important; border-radius: 999px; background: #E3F3EA; color: #1C7A58; white-space: nowrap; }
    .cms-table .cms-pill--off { background: #F0ECE1; color: #6B7570; }
    .cms-table .cms-pill--tag { background: #EEF0FB; color: #4A4FA3; padding: 4px 10px !important; font-size: 10.5px; }
    .cms-row-actions { display: flex; gap: 6px; align-items: center; flex-wrap: nowrap; white-space: nowrap; }
    .cms-row-actions form { margin: 0; display: inline-flex; }
    .cms-table .cms-mini { height: 34px; min-width: 34px; padding: 0 12px !important; border-radius: 8px; border: 1px solid #DDD5C2; background: #fff; color: #123A2E; font-size: 12.5px; font-weight: 700; line-height: 1; cursor: pointer; text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; }
    .cms-table .cms-mini:hover { background: #F6F4EF; }
    .cms-table .cms-mini[disabled] { opacity: .35; cursor: default; }
    .cms-table .cms-mini--danger { color: #B02A47; border-color: #F0C4CD; }
    .cms-table .cms-mini--danger:hover { background: #FDEEF1; }
    .cms-table .cms-mini--icon { padding: 0 !important; width: 34px; }
    .cms-qa b { display: block; color: #123A2E; font-size: 14px; }
    .cms-qa small { color: #8A938E; font-size: 12.5px; }
</style>
