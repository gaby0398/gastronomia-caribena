import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { IndexComponent } from './components/index/index.component';
import { NavbarComponent } from './components/nav-bar/nav-bar.component';


@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet,IndexComponent, NavbarComponent ],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
  export class AppComponent {
    title = 'presentacion';
    navigateToFacebook(): void {
    window.open('https://www.facebook.com/Proyecto-EC-594-Salvaguarda-la-Cultura-Gastronómica-del-Caribe-Limón', '_blank');
  }

}
