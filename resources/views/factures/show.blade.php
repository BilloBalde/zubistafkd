<!DOCTYPE html>
<html lang="en">
    @include('layouts.head')
    <body>
        <div id="global-loader">
            <div class="whirly-loader"> </div>
        </div>

        <div class="main-wrapper">
            @include('layouts.header')

            @include('layouts.sidebar')

            <div class="page-wrapper">
                <div class="content">
                    <div class="page-header">
                        <div class="page-title">
                            <h4>Details de Vente</h4>
                            <h6>Voire les detailles de vente</h6>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-sales-split">
                                <h2><span id="invoice_code">Detaille de la facture : {{ $facture }}</span></h2>
                                <ul>
                                    <li>
                                        <a href="{{ route('factures.index') }}"><img src="{{ asset('assets/img/icons/return1.svg') }}" alt="img"></a>
                                    </li>
                                    <li>
                                        <button id="downloadPdf"><img src="{{ asset('assets/img/icons/printer.svg') }}" alt="img"></button>
                                    </li>
                                </ul>
                            </div>
                            <div class="invoice-box table-height" style="max-width: 1600px;width:100%;overflow: auto;margin:15px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
                                <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
                                    <tbody>
                                        <tr class="top">
                                            <td colspan="6" style="padding: 5px;vertical-align: top;">
                                                <table style="width: 100%;line-height: inherit;text-align: left;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px">
                                                                <font style="vertical-align: inherit;margin-bottom:25px;"><font style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px; ">Informations Boutique</font></font><br>

                                                                @php $company = App\Models\Company::latest()->first(); @endphp
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;">
                                                                    @if($company)
                                                                        <img src="{{ asset('companies/'.$company->logo) }}" alt="img" class="me-2" style="width:40px;height:40px;">{{ $company->name }}
                                                                    @endif
                                                                    </font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="3a4d5b565117535417594f494e55575f487a5f425b574a565f14595557">{{ $user->email }}</a></font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> {{ $user->phone }}</font></font><br>
                                                            </td>
                                                            <td style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px">
                                                                <font style="vertical-align: inherit;margin-bottom:25px;"><font style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px; ">Informations Client</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> {{ $customer->customerName ?? '—' }}</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> {{ $customer->email ?? '' }}</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> {{ $customer->tel ?? '' }}</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> {{ $customer->address ?? '' }}</font></font><br>
                                                            </td>

                                                            <td style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px">
                                                                <font style="vertical-align: inherit;margin-bottom:25px;"><font style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px; ">Informations Facture</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> Reference </font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> Payment Status</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> Livraison Status</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> Date</font></font><br>
                                                            </td>
                                                            <td style="padding:5px;vertical-align:top;text-align:right;padding-bottom:20px">
                                                                <font style="vertical-align: inherit;margin-bottom:25px;"><font style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px; ">&nbsp;</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;">{{ $facture }} </font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:{{ ($laFacture->statut == "payé") ? '#2E7D32' : '#de2016' }};font-weight: 400;"> {{ $laFacture->statut }}</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:{{ ($laFacture->livraison == "livré") ? '#2E7D32' : '#de2016' }};font-weight: 400;"> {{ ($laFacture->livraison == "livré") ? 'livré' : 'non livré' }}</font></font><br>
                                                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;font-size: 14px;color:#000;font-weight: 400;"> {{ now()->format('d/m/Y') }}</font></font><br>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr class="heading " style="background: #F3F2F7;">
                                            <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                            Date Achat
                                            </td>
                                            <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                        Nom Produit
                                            </td>
                                            <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
            QTY
                                            </td>
                                            <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                            Prix
                                            </td>
                                            <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                            Montant
                                            </td>
                                        </tr>
                                        @foreach ($invoice as $item)
                                        <tr class="details" style="border-bottom:1px solid #E9ECEF ;">
                                            <td style="padding: 10px;vertical-align: top; ">
                                                {{ $item->created_at }}
                                            </td>
                                            <td style="padding: 10px;vertical-align: top; display: flex;align-items: center;">
                                                <img src="{{ asset('products/' . $item->produitImage) }}" alt="img" class="me-2" style="width:80px;height:100px;">
                                                {{ $item->produit }}
                                            </td>
                                            <td style="padding: 10px;vertical-align: top; ">
                                                {{ $item->quantity }}
                                            </td>
                                            <td style="padding: 10px;vertical-align: top; ">
                                                {{ number_format($item->prix, 0, ',', ' ') }} GNF
                                            </td>
                                            <td style="padding: 10px;vertical-align: top; ">
                                                {{ number_format($item->prixTotal, 0, ',', ' ') }} GNF
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <br>
                                <div class="row" style="page-break-inside: avoid;">
                                    <div class="col-lg-6 ">
                                        <div class="total-order w-100 max-widthauto m-auto mb-4">
                                            <ul>
                                                <li class="total">
                                                    <h4>Qtity Total</h4>
                                                    <h5>{{ $laFacture->quantity }} CTNS</h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 ">
                                        <div class="total-order w-100 max-widthauto m-auto mb-4">
                                            <ul>
                                                <li class="total">
                                                    <h4>Grand Total</h4>
                                                    <h5>{{ number_format($laFacture->montant_total, 0, ',', ' ') }} GNF</h5>
                                                </li>
                                                <br>
                                                @php
                                                    $i = 1;
                                                    $total_paid = 0;
                                                @endphp
                                                @foreach ($paiements as $item)
                                                <li class="total">
                                                    <h4>Paiement {{ $i }} : {{ $item->created_at }}</h4>
                                                    <h5>{{ number_format($item->versement, 0, ',', ' ') }} GNF</h5>
                                                </li>
                                                @php
                                                    $i++;
                                                    $total_paid += $item->versement;
                                                @endphp
                                                @endforeach
                                                <li class="total">
                                                    <h4>Total Paid</h4>
                                                    <h5>{{ number_format($total_paid, 0, ',', ' ') }} GNF</h5>
                                                </li>
                                                <li class="total">
                                                    <h4>Reste</h4>
                                                    <h5>{{ number_format($laFacture->reste, 0, ',', ' ') }} GNF</h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-lg-6 col-6">
                                        <p style="text-align:left">Client : {{ (isset($customer)) ? $customer->customerName : '' }}...................</p>
                                    </div>
                                    <div class="col-sm-6 col-lg-6 col-6">
                                        <p style="text-align:right">Gerant: {{ auth()->user()->name }} ...................</p>
                                    </div>
                                </div>
                                <br/>
                                <p style="text-align:center; font-weight:bold;">{{ $laFacture->store?->description ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @media print {
                .total-order {
                    page-break-inside: avoid;
                }
            }
        </style>
        @include('layouts.scripts')
        <script>
            document.getElementById('downloadPdf').addEventListener('click', function () {
            // Select the section you want to download
            var element = document.querySelector('.invoice-box');
            var code = document.querySelector('#invoice_code').textContent;
            // Configuration options for the PDF
            var opt = {
                margin:       0.5,
                filename:     code+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
            };

            // Create the PDF and download it
            html2pdf().from(element).set(opt).save();
        });

        </script>
    </body>
</html>



