---
name: Professional PDF Utility
colors:
  surface: '#faf8ff'
  surface-dim: '#d9d9e5'
  surface-bright: '#faf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3fe'
  surface-container: '#ededf9'
  surface-container-high: '#e7e7f3'
  surface-container-highest: '#e1e2ed'
  on-surface: '#191b23'
  on-surface-variant: '#434655'
  inverse-surface: '#2e3039'
  inverse-on-surface: '#f0f0fb'
  outline: '#737686'
  outline-variant: '#c3c6d7'
  surface-tint: '#0053db'
  primary: '#004ac6'
  on-primary: '#ffffff'
  primary-container: '#2563eb'
  on-primary-container: '#eeefff'
  inverse-primary: '#b4c5ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#943700'
  on-tertiary: '#ffffff'
  tertiary-container: '#bc4800'
  on-tertiary-container: '#ffede6'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dbe1ff'
  primary-fixed-dim: '#b4c5ff'
  on-primary-fixed: '#00174b'
  on-primary-fixed-variant: '#003ea8'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#ffdbcd'
  tertiary-fixed-dim: '#ffb596'
  on-tertiary-fixed: '#360f00'
  on-tertiary-fixed-variant: '#7d2d00'
  background: '#faf8ff'
  on-background: '#191b23'
  surface-variant: '#e1e2ed'
typography:
  h1:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.2'
    letterSpacing: 0.01em
  h2:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
    letterSpacing: 0.01em
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: 0.02em
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
    letterSpacing: 0.02em
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1.0'
    letterSpacing: 0.05em
  button:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.0'
    letterSpacing: 0.02em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  sidebar-width: 260px
  main-card-max-width: 800px
  container-padding: 2.5rem
  gutter: 1.5rem
  stack-gap: 1rem
---

## Brand & Style
The brand personality is efficient, reliable, and unobtrusive. It aims to evoke a sense of digital "lightness," transforming heavy document management into a seamless, airy experience. The target audience includes professionals and knowledge workers who require precision without the clutter of legacy software.

The design system follows a **Modern Corporate** aesthetic with a heavy emphasis on **Minimalism**. It utilizes expansive white space to reduce cognitive load during complex tasks like file merging or conversion. The interface feels high-end through its restraint, relying on rhythmic spacing and refined typography rather than decorative elements.

## Colors
The palette is rooted in a "Paper & Ink" philosophy. The primary action color, a sophisticated blue (#2563eb), is used sparingly to draw attention to critical calls-to-action and active states. 

The background (#f8fafc) provides a cool-toned, soft canvas that reduces eye strain compared to pure white. Neutral greys (#64748b) are used for secondary text to maintain a soft contrast ratio that is still highly legible. Status indicators for success and warnings use desaturated greens and ambers, ensuring they inform the user without breaking the calm, airy atmosphere of the interface.

## Typography
This design system uses **Inter** for its systematic, utilitarian precision. The typographic scale is designed for high readability in a utility-focused environment. 

Generous tracking (letter spacing) is applied to body text and labels to enhance the "airy" feel and prevent the UI from feeling cramped. Headlines are kept tight but distinct. Use the `label-caps` style for sidebar category headers and small metadata to create a clear structural hierarchy without adding visual weight.

## Layout & Spacing
The layout uses a **Fixed-Fluid Hybrid** model. A persistent sidebar is anchored to the left, providing immediate access to the primary utility tools. 

The main content area utilizes a centered card layout to focus the user's attention. This card has a maximum width to ensure content remains readable and accessible, surrounded by generous "safe area" margins. Spacing follows an 8px rhythmic grid, with a preference for larger gaps (24px+) between major sections to maintain the airy aesthetic. Elements within the main card should be stacked with consistent vertical rhythm.

## Elevation & Depth
Depth is conveyed through **Ambient Shadows** and **Tonal Layers**. Instead of harsh borders, the main interactive card utilizes a very soft, diffused shadow (0px 10px 25px rgba(0, 0, 0, 0.03)) to lift it from the background.

The sidebar is treated as a secondary surface, slightly darker or more translucent than the main card, creating a clear environmental hierarchy. Interactive elements like buttons and inputs should feel "nested" or "raised" using subtle inner shadows or 1px strokes in a slightly darker neutral shade than the background.

## Shapes
The design system employs a **Rounded** shape language to soften the industrial nature of PDF management. 

Standard components (buttons, input fields) use a 12px radius, while larger containers and the main tool card use a 16px radius. This creates a friendly, approachable silhouette. Icons must follow this logic, using line art with rounded terminals and subtle, low-opacity fills that echo the primary or status colors.

## Components
- **Buttons**: Use the primary blue (#2563eb) for "Action" buttons with white text. Secondary buttons should use a soft grey ghost style with a 1px border.
- **Main Tool Card**: A large, white surface with 16px rounded corners. This is the stage for "Drop Zones" and "File Lists."
- **Navigation Sidebar**: Icons should be 24px line art. Active states are indicated by a subtle background pill shape in a very light tint of the primary blue.
- **File Upload/Drop Zone**: A dashed border using the neutral text color at 20% opacity. Use a large icon and clear "body-lg" instructions.
- **Chips**: Used for file formats (e.g., .PDF, .JPG). Small, 4px rounded corners, with a very light neutral background.
- **Progress Bars**: Thin (4px - 6px) height, using the primary blue for the fill and the background neutral for the track, reflecting a minimalist take on utility status.