import { CommonModule } from '@angular/common';
import { Component, HostListener } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../shared/services/auth.service';

@Component({
  selector: 'app-boton-gestion-cerrar',
  standalone: true,
  imports: [CommonModule, MatIconModule, RouterModule],
  templateUrl: './boton-gestion-cerrar.component.html',
  styleUrls: ['./boton-gestion-cerrar.component.css'],
})
export class BotonGestionCerrarComponent {
  isOpen = false;

  constructor(private authService: AuthService, private router: Router) {}

  // Abre o cierra el menú al hacer clic en el botón
  toggleMenu() {
    this.isOpen = !this.isOpen;
  }

  // Escucha clics globales en el documento
  @HostListener('document:click', ['$event'])
  onClickOutside(event: Event) {
    const targetElement = event.target as HTMLElement;

    // Verifica si el clic ocurrió fuera del menú
    if (!targetElement.closest('.hamburger-container')) {
      this.isOpen = false; // Cierra el menú si está abierto
    }
  }

  // Maneja el clic en el botón de cerrar sesión
  cerrarSesion() {
    this.authService.logout();
    this.router.navigate(['/login']); // Redirigir a la página de inicio de sesión
  }
}
