<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rappel d'entretien</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .interview-card {
            background-color: #f8fafc;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .interview-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .label {
            font-weight: 600;
            color: #6b7280;
        }
        .value {
            color: #111827;
            font-weight: 500;
        }
        .urgent {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            border-left: 4px solid #ef4444;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .important {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .normal {
            background-color: #ecfdf5;
            border: 1px solid #10b981;
            border-left: 4px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .alert-icon {
            font-size: 18px;
            margin-right: 10px;
        }
        .urgent .alert-icon { color: #ef4444; }
        .important .alert-icon { color: #f59e0b; }
        .normal .alert-icon { color: #10b981; }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ApplyTracker</div>
            <h1 style="margin: 0; color: #111827;">Rappel d'entretien</h1>
        </div>

        <p>Bonjour {{ $user->name }},</p>

        <div class="{{ $urgencyLevel }}">
            <span class="alert-icon">
                @if($urgencyLevel === 'urgent')
                @elseif($urgencyLevel === 'important')
                @else
                @endif
            </span>
            <strong>
                @if($daysUntilInterview === 1)
                    Votre entretien est demain !
                @elseif($daysUntilInterview === 2)
                    Votre entretien approche ! Plus que 2 jours.
                @elseif($daysUntilInterview === 3)
                    Votre entretien approche ! N'oubliez pas votre rendez-vous dans 3 jours.
                @else
                    Votre entretien approche ! Plus que {{ $daysUntilInterview }} jours.
                @endif
            </strong>
        </div>

        <div class="interview-card">
            <h3 style="margin-top: 0; color: #10b981;">Détails de l'entretien</h3>
            
            <div class="interview-details">
                <div class="detail-row">
                    <span class="label">Entreprise :</span>
                    <span class="value">{{ $application->company }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="label">Poste :</span>
                    <span class="value">{{ $application->position }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="label">Date & Heure :</span>
                    <span class="value">
                        {{ \Carbon\Carbon::parse($application->interview_date)->format('d/m/Y à H:i') }}
                    </span>
                </div>

                @if($application->interview_location)
                <div class="detail-row">
                    <span class="label">Lieu :</span>
                    <span class="value">{{ $application->interview_location }}</span>
                </div>
                @endif

                @if($application->interview_type)
                <div class="detail-row">
                    <span class="label">Type :</span>
                    <span class="value">
                        @if($application->interview_type === 'video')
                            Entretien vidéo
                        @elseif($application->interview_type === 'phone')
                            Entretien téléphonique
                        @elseif($application->interview_type === 'in-person')
                            Entretien en présentiel
                        @else
                            {{ $application->interview_type }}
                        @endif
                    </span>
                </div>
                @endif
                
                @if($application->notes)
                <div style="margin-top: 15px;">
                    <span class="label">Notes :</span>
                    <p style="margin: 5px 0; font-style: italic; background-color: #f9fafb; padding: 10px; border-radius: 4px;">
                        {{ $application->notes }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        <div style="text-align: center;">
            <p><strong>Conseils pour bien vous préparer :</strong></p>
            <ul style="text-align: left; max-width: 450px; margin: 0 auto; background-color: #f9fafb; padding: 20px; border-radius: 8px;">
                @if($daysUntilInterview === 1)
                    <li><strong>Aujourd'hui :</strong> Vérifiez l'adresse/lien de connexion</li>
                    <li>Préparez vos vêtements et documents</li>
                    <li>Relisez vos notes sur l'entreprise</li>
                    <li>Dormez bien et arrivez détendu(e) !</li>
                @else
                    <li>Relisez votre CV et la description du poste</li>
                    <li>Recherchez des informations récentes sur l'entreprise</li>
                    <li>Préparez des questions pertinentes à poser</li>
                    @if($application->interview_type === 'video')
                        <li>Testez votre caméra et votre connexion internet</li>
                    @endif
                    <li>Prévoyez d'arriver 10 minutes en avance</li>
                @endif
            </ul>
        </div>

        @if($daysUntilInterview === 1)
        <div style="text-align: center; background-color: #dbeafe; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0; font-weight: 600; color: #1e40af;">
                C'est le moment de briller ! Vous êtes prêt(e) pour cet entretien.
            </p>
        </div>
        @endif

        <div class="footer">
            <p>
                @if($daysUntilInterview === 1)
                    Bonne chance pour demain !
                @else
                    Bonne chance pour votre entretien !
                @endif
            </p>
            <p>Cet email a été envoyé automatiquement par ApplyTracker</p>
            <p style="margin-top: 10px; font-size: 12px;">
                Entretien dans {{ $daysUntilInterview }} jour{{ $daysUntilInterview > 1 ? 's' : '' }}
            </p>
        </div>
    </div>
</body>
</html>
