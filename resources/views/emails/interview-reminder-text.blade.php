Bonjour {{ $user->name }},

RAPPEL D'ENTRETIEN - Dans 3 jours !

Détails de votre entretien :
• Entreprise : {{ $application->company }}
• Poste : {{ $application->position }}
• Date & Heure : {{ $interviewDate }}
@if($application->notes)
• Notes : {{ $application->notes }}
@endif

Conseils de préparation :
- Relisez votre CV et la description du poste
- Préparez des questions sur l'entreprise
- Testez votre connexion si c'est un entretien vidéo
- Prévoyez d'arriver 10 minutes en avance

Bonne chance ! 🍀

---
Cet email a été envoyé automatiquement par JobTracker