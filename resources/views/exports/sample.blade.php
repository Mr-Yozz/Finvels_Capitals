<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Repayment Schedule - A4 Format</title>
    <style>
        /* General Styles for the document */
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10pt; 
            margin: 0; 
            padding: 10mm; /* General padding */
        }

        /* A4 Print Optimization */
        @page {
            size: A4; /* Define the page size as A4 */
            margin: 10mm; /* Minimal margins for print */
        }

        /* Header Tables */
        .header-table, .main-data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 5px; 
        }
        .header-table td { 
            padding: 3px; 
            vertical-align: top; 
            font-size: 9pt;
        }
        .loan-details td { 
            padding: 3px 0; 
        }
        
        /* Two-Column Table Container for Repayment Schedule */
        .table-container { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 10px;
        }
        .table-col { 
            width: 49%; /* Ensures tables sit side-by-side on A4 */
        }

        /* Repayment Table Styles */
        .repayment-table {
            width: 100%;
            border-collapse: collapse;
            /* Prevents the table from being split across columns if possible */
            page-break-inside: avoid; 
        }
        .repayment-table th, .repayment-table td { 
            border: 1px solid black; 
            padding: 2px; 
            text-align: right; 
            font-size: 7pt; /* Smaller font for A4 density */
            white-space: nowrap; /* Keeps data in one line */
        }
        .repayment-table th { 
            text-align: center; 
            background-color: #f0f0f0; 
            font-weight: bold;
        }
        .repayment-table td:nth-child(2) { /* Date column */
            text-align: center;
        }
        .repayment-table td:nth-child(1), .repayment-table td:nth-child(7) { /* Inst. No and Signature */
            text-align: center;
        }
        
        /* Specific header cell styling */
        .main-data-table td {
            border: 1px solid black; 
            padding: 4px;
            text-align: center;
            font-size: 9pt;
        }
        .main-data-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-data-table table td {
            border: none;
            padding: 0;
            font-size: 9pt;
        }
        .main-data-table .split-cell td:first-child {
             border-right: 1px solid black;
        }

    </style>
</head>
<body>
    @php
    $loan = $invoice->loan ?? null;
    $member = $loan->member ?? null;
    $branch = $loan->branch ?? null;
    // totals from invoice lines
    $lines = $invoice->lines ?? collect([]);
    if (is_array($lines)) $lines = collect($lines);
    $totalPrincipal = $lines->sum(fn($l)=> (float) data_get($l,'principal',0));
    $totalInterest = $lines->sum(fn($l)=> (float) data_get($l,'interest',0));
    $totalLineTotal = $lines->sum(fn($l)=> (float) data_get($l,'total',0));
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                </td>
            <td style="width: 50%; text-align: right;">
                <p><strong>GrameenKoota</strong></p>
            </td>
        </tr>
    </table>
    

    <table class="header-table loan-details">
        <tr>
            <td><strong>Member ID :</strong> {{ $member->member_id ?? ($member->id ?? '-') }}</td>
            <td><strong>Branch :</strong> {{ $branch->name ?? '-' }}</td>
            <td><strong>Member Name:</strong> {{ $member->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Loan Account :</strong> {{ $member->name ?? '-' }}</td>
            <td><strong>Product :</strong> {{ data_get($loan,'product.name', $loan->product_name ?? 'Business Loan') }}</td>
            <td><strong>Disbursed On:</strong> {{ optional($loan->disbursed_at)->format('d M Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Loan Amount :</strong> ₹ {{ number_format((float) ($loan->principal ?? $invoice->amount ?? 0), 2) }}</td>
            <td><strong>Tenure :</strong> {{ $loan->tenure_months ?? ($loan->tenure ?? '-') }}</td>
            <td><strong>Processing Fee:</strong>₹ {{ number_format((float) ($loan->processing_fee ?? 0), 2) }}</td>
        </tr>
        {% comment %} <tr>
            <td><strong>Loan Purpose :</strong> TAILORING MACHINE</td>
            <td><strong>Disbursement Date :</strong> 15 Nov 2025</td>
            <td><strong>Term of Loan:</strong> 104(WEEKLY)</td>
        </tr> {% endcomment %}
    </table>

    <table class="main-data-table" style="width: 100%;">
        <tr>
            <td style="width: 20%;">Processing Fees/Pre-FET Upload Collection</td>
            <td style="width: 10%;">Rs 825</td>
            <td style="width: 20%;">(1.5% of Loan amt exclude GST)</td>
            <td style="width: 15%;">Insurance Premium
                <table class="split-cell">
                    <tr>
                        <td>Member</td>
                        <td>Spouse</td>
                    </tr>
                </table>
            </td>
            <td style="width: 10%;">Rs 1430</td>
            <td style="width: 25%;">Phone: 7944119040</td>
        </tr>
    </table>

    <div class="table-container">
        <div class="table-col">
            <table class="repayment-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">Inst. No.</th>
                        <th style="width: 5%;">Date</th>
                        <th style="width: 5%;">Principal</th>
                        <th style="width: 5%;">Interest</th>
                        <th style="width: 5%;">Total</th>
                        <th style="width: 5%;">Prin OS</th>
                        <th style="width: 5%;">KM Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($left_rows as $line)
                    <tr><td>{{ $line->inst_no }}</td><td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M Y') }}</td><td class="right-align">{{ number_format($line->principal,2) }}</td><td class="right-align">{{ number_format($line->interest,2) }}</td><td class="right-align">{{ number_format($line->total,2) }}</td><td class="right-align">{{ number_format($line->os,2) }}</td><td></td></tr>
                    {% comment %} <tr><td>2</td><td>29 Nov 2025</td><td class="right-align">Rs 48.92</td><td class="right-align">Rs 244.69</td><td class="right-align">Rs 293.61</td><td class="right-align">Rs 50951.08</td><td></td></tr>
                    <tr><td>3</td><td>06 Dec 2025</td><td class="right-align">Rs 48.98</td><td class="right-align">Rs 244.46</td><td class="right-align">Rs 293.44</td><td class="right-align">Rs 50902.10</td><td></td></tr>
                    <tr><td>4</td><td>13 Dec 2025</td><td class="right-align">Rs 49.03</td><td class="right-align">Rs 244.23</td><td class="right-align">Rs 293.26</td><td class="right-align">Rs 50853.07</td><td></td></tr>
                    <tr><td>5</td><td>20 Dec 2025</td><td class="right-align">Rs 49.08</td><td class="right-align">Rs 244.00</td><td class="right-align">Rs 293.08</td><td class="right-align">Rs 50803.99</td><td></td></tr>
                    <tr><td>6</td><td>27 Dec 2025</td><td class="right-align">Rs 437.26</td><td class="right-align">Rs 243.76</td><td class="right-align">Rs 681.02</td><td class="right-align">Rs 50366.73</td><td></td></tr>
                    <tr><td>7</td><td>03 Jan 2026</td><td class="right-align">Rs 437.54</td><td class="right-align">Rs 241.67</td><td class="right-align">Rs 679.21</td><td class="right-align">Rs 49929.19</td><td></td></tr>
                    <tr><td>8</td><td>10 Jan 2026</td><td class="right-align">Rs 437.82</td><td class="right-align">Rs 239.58</td><td class="right-align">Rs 677.40</td><td class="right-align">Rs 49491.37</td><td></td></tr>
                    <tr><td>9</td><td>17 Jan 2026</td><td class="right-align">Rs 438.10</td><td class="right-align">Rs 237.48</td><td class="right-align">Rs 675.58</td><td class="right-align">Rs 49053.27</td><td></td></tr>
                    <tr><td>10</td><td>24 Jan 2026</td><td class="right-align">Rs 438.38</td><td class="right-align">Rs 235.39</td><td class="right-align">Rs 673.77</td><td class="right-align">Rs 48614.89</td><td></td></tr>
                    <tr><td>11</td><td>31 Jan 2026</td><td class="right-align">Rs 438.66</td><td class="right-align">Rs 233.29</td><td class="right-align">Rs 671.95</td><td class="right-align">Rs 48176.23</td><td></td></tr>
                    <tr><td>12</td><td>07 Feb 2026</td><td class="right-align">Rs 438.94</td><td class="right-align">Rs 231.18</td><td class="right-align">Rs 670.12</td><td class="right-align">Rs 47737.29</td><td></td></tr>
                    <tr><td>13</td><td>14 Feb 2026</td><td class="right-align">Rs 443.46</td><td class="right-align">Rs 229.07</td><td class="right-align">Rs 672.53</td><td class="right-align">Rs 47293.83</td><td></td></tr>
                    <tr><td>14</td><td>21 Feb 2026</td><td class="right-align">Rs 443.74</td><td class="right-align">Rs 226.96</td><td class="right-align">Rs 670.70</td><td class="right-align">Rs 46850.09</td><td></td></tr>
                    <tr><td>15</td><td>28 Feb 2026</td><td class="right-align">Rs 444.03</td><td class="right-align">Rs 224.84</td><td class="right-align">Rs 668.87</td><td class="right-align">Rs 46406.06</td><td></td></tr>
                    <tr><td>16</td><td>07 Mar 2026</td><td class="right-align">Rs 447.88</td><td class="right-align">Rs 222.72</td><td class="right-align">Rs 670.60</td><td class="right-align">Rs 45958.18</td><td></td></tr>
                    <tr><td>17</td><td>14 Mar 2026</td><td class="right-align">Rs 448.16</td><td class="right-align">Rs 220.60</td><td class="right-align">Rs 668.76</td><td class="right-align">Rs 45510.02</td><td></td></tr>
                    <tr><td>18</td><td>21 Mar 2026</td><td class="right-align">Rs 448.45</td><td class="right-align">Rs 218.47</td><td class="right-align">Rs 666.92</td><td class="right-align">Rs 45061.57</td><td></td></tr>
                    <tr><td>19</td><td>28 Mar 2026</td><td class="right-align">Rs 453.26</td><td class="right-align">Rs 216.33</td><td class="right-align">Rs 669.59</td><td class="right-align">Rs 44608.31</td><td></td></tr>
                    <tr><td>20</td><td>04 Apr 2026</td><td class="right-align">Rs 453.55</td><td class="right-align">Rs 214.19</td><td class="right-align">Rs 667.74</td><td class="right-align">Rs 44154.76</td><td></td></tr>
                    <tr><td>21</td><td>11 Apr 2026</td><td class="right-align">Rs 453.84</td><td class="right-align">Rs 212.05</td><td class="right-align">Rs 665.89</td><td class="right-align">Rs 43700.92</td><td></td></tr>
                    <tr><td>22</td><td>18 Apr 2026</td><td class="right-align">Rs 454.13</td><td class="right-align">Rs 209.90</td><td class="right-align">Rs 664.03</td><td class="right-align">Rs 43246.79</td><td></td></tr>
                    <tr><td>23</td><td>25 Apr 2026</td><td class="right-align">Rs 454.42</td><td class="right-align">Rs 207.75</td><td class="right-align">Rs 662.17</td><td class="right-align">Rs 42792.37</td><td></td></tr>
                    <tr><td>24</td><td>02 May 2026</td><td class="right-align">Rs 459.72</td><td class="right-align">Rs 205.60</td><td class="right-align">Rs 665.32</td><td class="right-align">Rs 42332.65</td><td></td></tr>
                    <tr><td>25</td><td>09 May 2026</td><td class="right-align">Rs 460.01</td><td class="right-align">Rs 203.44</td><td class="right-align">Rs 663.45</td><td class="right-align">Rs 41872.64</td><td></td></tr>
                    <tr><td>26</td><td>16 May 2026</td><td class="right-align">Rs 465.35</td><td class="right-align">Rs 201.27</td><td class="right-align">Rs 666.62</td><td class="right-align">Rs 41407.29</td><td></td></tr>
                    <tr><td>27</td><td>23 May 2026</td><td class="right-align">Rs 465.64</td><td class="right-align">Rs 199.10</td><td class="right-align">Rs 664.74</td><td class="right-align">Rs 40941.65</td><td></td></tr>
                    <tr><td>28</td><td>30 May 2026</td><td class="right-align">Rs 465.94</td><td class="right-align">Rs 196.92</td><td class="right-align">Rs 662.86</td><td class="right-align">Rs 40475.71</td><td></td></tr>
                    <tr><td>29</td><td>06 Jun 2026</td><td class="right-align">Rs 471.68</td><td class="right-align">Rs 194.74</td><td class="right-align">Rs 666.42</td><td class="right-align">Rs 40004.03</td><td></td></tr>
                    <tr><td>30</td><td>13 Jun 2026</td><td class="right-align">Rs 471.97</td><td class="right-align">Rs 192.55</td><td class="right-align">Rs 664.52</td><td class="right-align">Rs 39532.06</td><td></td></tr>
                    <tr><td>31</td><td>20 Jun 2026</td><td class="right-align">Rs 472.27</td><td class="right-align">Rs 190.35</td><td class="right-align">Rs 662.62</td><td class="right-align">Rs 39059.79</td><td></td></tr>
                    <tr><td>32</td><td>27 Jun 2026</td><td class="right-align">Rs 472.56</td><td class="right-align">Rs 188.16</td><td class="right-align">Rs 660.72</td><td class="right-align">Rs 38587.23</td><td></td></tr>
                    <tr><td>33</td><td>04 Jul 2026</td><td class="right-align">Rs 478.71</td><td class="right-align">Rs 185.95</td><td class="right-align">Rs 664.66</td><td class="right-align">Rs 38108.52</td><td></td></tr>
                    <tr><td>34</td><td>11 Jul 2026</td><td class="right-align">Rs 479.01</td><td class="right-align">Rs 183.74</td><td class="right-align">Rs 662.75</td><td class="right-align">Rs 37629.51</td><td></td></tr>
                    <tr><td>35</td><td>18 Jul 2026</td><td class="right-align">Rs 479.31</td><td class="right-align">Rs 181.53</td><td class="right-align">Rs 660.84</td><td class="right-align">Rs 37150.20</td><td></td></tr>
                    <tr><td>36</td><td>25 Jul 2026</td><td class="right-align">Rs 479.61</td><td class="right-align">Rs 179.32</td><td class="right-align">Rs 658.93</td><td class="right-align">Rs 36670.59</td><td></td></tr>
                    <tr><td>37</td><td>01 Aug 2026</td><td class="right-align">Rs 486.29</td><td class="right-align">Rs 177.10</td><td class="right-align">Rs 663.39</td><td class="right-align">Rs 36184.30</td><td></td></tr>
                    <tr><td>38</td><td>08 Aug 2026</td><td class="right-align">Rs 486.59</td><td class="right-align">Rs 174.88</td><td class="right-align">Rs 661.47</td><td class="right-align">Rs 35697.71</td><td></td></tr>
                    <tr><td>39</td><td>15 Aug 2026</td><td class="right-align">Rs 486.89</td><td class="right-align">Rs 172.66</td><td class="right-align">Rs 659.55</td><td class="right-align">Rs 35210.82</td><td></td></tr>
                    <tr><td>40</td><td>22 Aug 2026</td><td class="right-align">Rs 487.19</td><td class="right-align">Rs 170.43</td><td class="right-align">Rs 657.62</td><td class="right-align">Rs 34723.63</td><td></td></tr>
                    <tr><td>41</td><td>29 Aug 2026</td><td class="right-align">Rs 494.34</td><td class="right-align">Rs 168.19</td><td class="right-align">Rs 662.53</td><td class="right-align">Rs 34229.29</td><td></td></tr>
                    <tr><td>42</td><td>05 Sep 2026</td><td class="right-align">Rs 494.65</td><td class="right-align">Rs 165.96</td><td class="right-align">Rs 660.61</td><td class="right-align">Rs 33734.64</td><td></td></tr>
                    <tr><td>43</td><td>12 Sep 2026</td><td class="right-align">Rs 494.95</td><td class="right-align">Rs 163.72</td><td class="right-align">Rs 658.67</td><td class="right-align">Rs 33239.69</td><td></td></tr>
                    <tr><td>44</td><td>19 Sep 2026</td><td class="right-align">Rs 495.26</td><td class="right-align">Rs 161.48</td><td class="right-align">Rs 656.74</td><td class="right-align">Rs 32744.43</td><td></td></tr>
                    <tr><td>45</td><td>26 Sep 2026</td><td class="right-align">Rs 503.45</td><td class="right-align">Rs 159.23</td><td class="right-align">Rs 662.68</td><td class="right-align">Rs 32240.98</td><td></td></tr>
                    <tr><td>46</td><td>03 Oct 2026</td><td class="right-align">Rs 503.76</td><td class="right-align">Rs 156.98</td><td class="right-align">Rs 660.74</td><td class="right-align">Rs 31737.22</td><td></td></tr>
                    <tr><td>47</td><td>10 Oct 2026</td><td class="right-align">Rs 504.07</td><td class="right-align">Rs 154.72</td><td class="right-align">Rs 658.79</td><td class="right-align">Rs 31233.15</td><td></td></tr>
                    <tr><td>48</td><td>17 Oct 2026</td><td class="right-align">Rs 504.38</td><td class="right-align">Rs 152.47</td><td class="right-align">Rs 656.85</td><td class="right-align">Rs 30728.77</td><td></td></tr>
                    <tr><td>49</td><td>24 Oct 2026</td><td class="right-align">Rs 513.74</td><td class="right-align">Rs 150.21</td><td class="right-align">Rs 663.95</td><td class="right-align">Rs 30215.03</td><td></td></tr>
                    <tr><td>50</td><td>31 Oct 2026</td><td class="right-align">Rs 514.05</td><td class="right-align">Rs 147.94</td><td class="right-align">Rs 661.99</td><td class="right-align">Rs 29700.98</td><td></td></tr>
                    <tr><td>51</td><td>07 Nov 2026</td><td class="right-align">Rs 514.37</td><td class="right-align">Rs 145.68</td><td class="right-align">Rs 660.05</td><td class="right-align">Rs 29186.61</td><td></td></tr>
                    <tr><td>52</td><td>14 Nov 2026</td><td class="right-align">Rs 514.68</td><td class="right-align">Rs 143.40</td><td class="right-align">Rs 658.08</td><td class="right-align">Rs 28671.93</td><td></td></tr> {% endcomment %}
                </tbody>
            </table>
        </div>

        <div class="table-col">
            <table class="repayment-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">Inst. No.</th>
                        <th style="width: 5%;">Date</th>
                        <th style="width: 5%;">Principal</th>
                        <th style="width: 5%;">Interest</th>
                        <th style="width: 5%;">Total</th>
                        <th style="width: 5%;">Prin OS</th>
                        <th style="width: 5%;">KM Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($right_rows as $line)
                    <tr><td>{{ $line->inst_no }}</td><td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M Y') }}</td><td class="right-align">{{ number_format($line->principal,2) }}</td><td class="right-align">{{ number_format($line->interest,2) }}</td><td class="right-align">{{ number_format($line->total,2) }}</td><td class="right-align">{{ number_format($line->os,2) }}</td><td></td></tr>
                    {% comment %} <tr><td>54</td><td>28 Nov 2026</td><td class="right-align">Rs 524.72</td><td class="right-align">Rs 138.85</td><td class="right-align">Rs 663.57</td><td class="right-align">Rs 27622.80</td><td></td></tr>
                    <tr><td>55</td><td>05 Dec 2026</td><td class="right-align">Rs 525.04</td><td class="right-align">Rs 136.57</td><td class="right-align">Rs 661.61</td><td class="right-align">Rs 27097.76</td><td></td></tr>
                    <tr><td>56</td><td>12 Dec 2026</td><td class="right-align">Rs 535.53</td><td class="right-align">Rs 134.28</td><td class="right-align">Rs 669.81</td><td class="right-align">Rs 26562.23</td><td></td></tr>
                    <tr><td>57</td><td>19 Dec 2026</td><td class="right-align">Rs 535.85</td><td class="right-align">Rs 131.99</td><td class="right-align">Rs 667.84</td><td class="right-align">Rs 26026.38</td><td></td></tr>
                    <tr><td>58</td><td>26 Dec 2026</td><td class="right-align">Rs 536.17</td><td class="right-align">Rs 129.69</td><td class="right-align">Rs 665.86</td><td class="right-align">Rs 25490.21</td><td></td></tr>
                    <tr><td>59</td><td>02 Jan 2027</td><td class="right-align">Rs 547.01</td><td class="right-align">Rs 127.39</td><td class="right-align">Rs 674.40</td><td class="right-align">Rs 24943.20</td><td></td></tr>
                    <tr><td>60</td><td>09 Jan 2027</td><td class="right-align">Rs 547.33</td><td class="right-align">Rs 125.08</td><td class="right-align">Rs 672.41</td><td class="right-align">Rs 24395.87</td><td></td></tr>
                    <tr><td>61</td><td>16 Jan 2027</td><td class="right-align">Rs 547.65</td><td class="right-align">Rs 122.77</td><td class="right-align">Rs 670.42</td><td class="right-align">Rs 23848.22</td><td></td></tr>
                    <tr><td>62</td><td>23 Jan 2027</td><td class="right-align">Rs 547.97</td><td class="right-align">Rs 120.46</td><td class="right-align">Rs 668.43</td><td class="right-align">Rs 23300.25</td><td></td></tr>
                    <tr><td>63</td><td>30 Jan 2027</td><td class="right-align">Rs 559.22</td><td class="right-align">Rs 118.14</td><td class="right-align">Rs 677.36</td><td class="right-align">Rs 22741.03</td><td></td></tr>
                    <tr><td>64</td><td>06 Feb 2027</td><td class="right-align">Rs 559.54</td><td class="right-align">Rs 115.82</td><td class="right-align">Rs 675.36</td><td class="right-align">Rs 22181.49</td><td></td></tr>
                    <tr><td>65</td><td>13 Feb 2027</td><td class="right-align">Rs 559.86</td><td class="right-align">Rs 113.49</td><td class="right-align">Rs 673.35</td><td class="right-align">Rs 21621.63</td><td></td></tr>
                    <tr><td>66</td><td>20 Feb 2027</td><td class="right-align">Rs 560.19</td><td class="right-align">Rs 111.16</td><td class="right-align">Rs 671.35</td><td class="right-align">Rs 21061.44</td><td></td></tr>
                    <tr><td>67</td><td>27 Feb 2027</td><td class="right-align">Rs 560.51</td><td class="right-align">Rs 108.83</td><td class="right-align">Rs 669.34</td><td class="right-align">Rs 20500.93</td><td></td></tr>
                    <tr><td>68</td><td>06 Mar 2027</td><td class="right-align">Rs 572.29</td><td class="right-align">Rs 106.49</td><td class="right-align">Rs 678.78</td><td class="right-align">Rs 19928.64</td><td></td></tr>
                    <tr><td>69</td><td>13 Mar 2027</td><td class="right-align">Rs 572.61</td><td class="right-align">Rs 104.15</td><td class="right-align">Rs 676.76</td><td class="right-align">Rs 19356.03</td><td></td></tr>
                    <tr><td>70</td><td>20 Mar 2027</td><td class="right-align">Rs 572.94</td><td class="right-align">Rs 101.80</td><td class="right-align">Rs 674.74</td><td class="right-align">Rs 18783.09</td><td></td></tr>
                    <tr><td>71</td><td>27 Mar 2027</td><td class="right-align">Rs 573.26</td><td class="right-align">Rs 99.45</td><td class="right-align">Rs 672.71</td><td class="right-align">Rs 18209.83</td><td></td></tr>
                    <tr><td>72</td><td>03 Apr 2027</td><td class="right-align">Rs 585.55</td><td class="right-align">Rs 97.09</td><td class="right-align">Rs 682.64</td><td class="right-align">Rs 17624.28</td><td></td></tr>
                    <tr><td>73</td><td>10 Apr 2027</td><td class="right-align">Rs 585.88</td><td class="right-align">Rs 94.72</td><td class="right-align">Rs 680.60</td><td class="right-align">Rs 17038.40</td><td></td></tr>
                    <tr><td>74</td><td>17 Apr 2027</td><td class="right-align">Rs 586.21</td><td class="right-align">Rs 92.35</td><td class="right-align">Rs 678.56</td><td class="right-align">Rs 16452.19</td><td></td></tr>
                    <tr><td>75</td><td>24 Apr 2027</td><td class="right-align">Rs 586.54</td><td class="right-align">Rs 89.97</td><td class="right-align">Rs 676.51</td><td class="right-align">Rs 15865.65</td><td></td></tr>
                    <tr><td>76</td><td>01 May 2027</td><td class="right-align">Rs 599.37</td><td class="right-align">Rs 87.59</td><td class="right-align">Rs 686.96</td><td class="right-align">Rs 15266.28</td><td></td></tr>
                    <tr><td>77</td><td>08 May 2027</td><td class="right-align">Rs 599.70</td><td class="right-align">Rs 85.20</td><td class="right-align">Rs 684.90</td><td class="right-align">Rs 14666.58</td><td></td></tr>
                    <tr><td>78</td><td>15 May 2027</td><td class="right-align">Rs 600.03</td><td class="right-align">Rs 82.81</td><td class="right-align">Rs 682.84</td><td class="right-align">Rs 14066.55</td><td></td></tr>
                    <tr><td>79</td><td>22 May 2027</td><td class="right-align">Rs 600.36</td><td class="right-align">Rs 80.41</td><td class="right-align">Rs 680.77</td><td class="right-align">Rs 13466.19</td><td></td></tr>
                    <tr><td>80</td><td>29 May 2027</td><td class="right-align">Rs 613.84</td><td class="right-align">Rs 78.01</td><td class="right-align">Rs 691.85</td><td class="right-align">Rs 12852.35</td><td></td></tr>
                    <tr><td>81</td><td>05 Jun 2027</td><td class="right-align">Rs 614.17</td><td class="right-align">Rs 75.60</td><td class="right-align">Rs 689.77</td><td class="right-align">Rs 12238.18</td><td></td></tr>
                    <tr><td>82</td><td>12 Jun 2027</td><td class="right-align">Rs 614.51</td><td class="right-align">Rs 73.18</td><td class="right-align">Rs 687.69</td><td class="right-align">Rs 11623.67</td><td></td></tr>
                    <tr><td>83</td><td>19 Jun 2027</td><td class="right-align">Rs 614.84</td><td class="right-align">Rs 70.76</td><td class="right-align">Rs 685.60</td><td class="right-align">Rs 11008.83</td><td></td></tr>
                    <tr><td>84</td><td>26 Jun 2027</td><td class="right-align">Rs 628.90</td><td class="right-align">Rs 68.34</td><td class="right-align">Rs 697.24</td><td class="right-align">Rs 10379.93</td><td></td></tr>
                    <tr><td>85</td><td>03 Jul 2027</td><td class="right-align">Rs 629.23</td><td class="right-align">Rs 65.91</td><td class="right-align">Rs 695.14</td><td class="right-align">Rs 9750.70</td><td></td></tr>
                    <tr><td>86</td><td>10 Jul 2027</td><td class="right-align">Rs 629.57</td><td class="right-align">Rs 63.47</td><td class="right-align">Rs 693.04</td><td class="right-align">Rs 9121.13</td><td></td></tr>
                    <tr><td>87</td><td>17 Jul 2027</td><td class="right-align">Rs 630.25</td><td class="right-align">Rs 61.03</td><td class="right-align">Rs 691.28</td><td class="right-align">Rs 8490.88</td><td></td></tr>
                    <tr><td>88</td><td>24 Jul 2027</td><td class="right-align">Rs 630.95</td><td class="right-align">Rs 58.58</td><td class="right-align">Rs 689.53</td><td class="right-align">Rs 7859.93</td><td></td></tr>
                    <tr><td>89</td><td>31 Jul 2027</td><td class="right-align">Rs 631.65</td><td class="right-align">Rs 56.12</td><td class="right-align">Rs 687.77</td><td class="right-align">Rs 7228.28</td><td></td></tr>
                    <tr><td>90</td><td>07 Aug 2027</td><td class="right-align">Rs 647.78</td><td class="right-align">Rs 53.66</td><td class="right-align">Rs 701.44</td><td class="right-align">Rs 6580.50</td><td></td></tr>
                    <tr><td>91</td><td>14 Aug 2027</td><td class="right-align">Rs 648.48</td><td class="right-align">Rs 51.19</td><td class="right-align">Rs 699.67</td><td class="right-align">Rs 5932.02</td><td></td></tr>
                    <tr><td>92</td><td>21 Aug 2027</td><td class="right-align">Rs 649.19</td><td class="right-align">Rs 48.72</td><td class="right-align">Rs 697.91</td><td class="right-align">Rs 5282.83</td><td></td></tr>
                    <tr><td>93</td><td>28 Aug 2027</td><td class="right-align">Rs 649.90</td><td class="right-align">Rs 46.24</td><td class="right-align">Rs 696.14</td><td class="right-align">Rs 4632.93</td><td></td></tr>
                    <tr><td>94</td><td>04 Sep 2027</td><td class="right-align">Rs 666.90</td><td class="right-align">Rs 43.76</td><td class="right-align">Rs 710.66</td><td class="right-align">Rs 3966.03</td><td></td></tr>
                    <tr><td>95</td><td>11 Sep 2027</td><td class="right-align">Rs 667.61</td><td class="right-align">Rs 41.27</td><td class="right-align">Rs 708.88</td><td class="right-align">Rs 3298.42</td><td></td></tr>
                    <tr><td>96</td><td>18 Sep 2027</td><td class="right-align">Rs 668.32</td><td class="right-align">Rs 38.77</td><td class="right-align">Rs 707.09</td><td class="right-align">Rs 2630.10</td><td></td></tr>
                    <tr><td>97</td><td>25 Sep 2027</td><td class="right-align">Rs 669.04</td><td class="right-align">Rs 36.27</td><td class="right-align">Rs 705.31</td><td class="right-align">Rs 1961.06</td><td></td></tr>
                    <tr><td>98</td><td>02 Oct 2027</td><td class="right-align">Rs 687.27</td><td class="right-align">Rs 33.77</td><td class="right-align">Rs 721.04</td><td class="right-align">Rs 1273.79</td><td></td></tr>
                    <tr><td>99</td><td>09 Oct 2027</td><td class="right-align">Rs 687.98</td><td class="right-align">Rs 31.26</td><td class="right-align">Rs 719.24</td><td class="right-align">Rs 585.81</td><td></td></tr>
                    <tr><td>100</td><td>16 Oct 2027</td><td class="right-align">Rs 677.06</td><td class="right-align">Rs 28.74</td><td class="right-align">Rs 705.80</td><td class="right-align">Rs 585.81</td><td></td></tr>
                    <tr><td>101</td><td>23 Oct 2027</td><td class="right-align">Rs 677.78</td><td class="right-align">Rs 26.21</td><td class="right-align">Rs 703.99</td><td class="right-align">Rs 585.81</td><td></td></tr>
                    <tr><td>102</td><td>30 Oct 2027</td><td class="right-align">Rs 678.50</td><td class="right-align">Rs 23.67</td><td class="right-align">Rs 702.17</td><td class="right-align">Rs 585.81</td><td></td></tr>
                    <tr><td>103</td><td>06 Nov 2027</td><td class="right-align">Rs 679.23</td><td class="right-align">Rs 21.13</td><td class="right-align">Rs 700.36</td><td class="right-align">Rs 585.81</td><td></td></tr>
                    <tr><td>104</td><td>13 Nov 2027</td><td class="right-align">Rs 679.95</td><td class="right-align">Rs 18.58</td><td class="right-align">Rs 698.53</td><td class="right-align">Rs 585.81</td><td></td></tr> {% endcomment %}
                </tbody>
            </table>
        </div>
    </div>

    <p style="font-size: 8pt; margin-top: 5px;">*Other terms are as per the Sanction Letter issued.</p>
    
    <table class="main-data-table" style="width: 100%; border: 1px solid black; margin-top: 10px;">
        <tr>
            <td style="border-right: 1px solid black; width: 50%; padding: 5px; text-align: left; border-top: none;">
                Loan Closed Date:
            </td>
            <td style="width: 50%; padding: 5px; text-align: left; border-top: none;">
                KM Signature
            </td>
        </tr>
    </table>

    <p style="font-size: 8pt; margin-top: 5px;">November 15, 2025 12:36 PM</p>

</body>
</html>