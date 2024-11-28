import { CommonModule } from '@angular/common';
import { Component, HostListener } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-boton-gestion-cerrar',
  standalone: true,
  imports: [CommonModule, MatIconModule, RouterModule], 
  templateUrl: './boton-gestion-cerrar.component.html',
  styleUrls: ['./boton-gestion-cerrar.component.css'],
})
export class BotonGestionCerrarComponent {
  isOpen = false;

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


}

function onClickOutside(event: Event | undefined, Event: { new(type: string, eventInitDict?: EventInit): Event; prototype: Event; readonly NONE: 0; readonly CAPTURING_PHASE: 1; readonly AT_TARGET: 2; readonly BUBBLING_PHASE: 3; }) {
  throw new Error('Function not implemented.');
}

